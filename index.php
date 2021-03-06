    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="utf-8">
    <!--
    Gamepad API Test
    To the extent possible under law, the author(s) have dedicated all copyright and related and neighboring rights to this software to the public domain worldwide. This software is distributed without any warranty.
    You should have received a copy of the CC0 Public Domain Dedication along with this software. If not, see <http://creativecommons.org/publicdomain/zero/1.0/>.
    -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <style>
      

    .disable-select {
      user-select: none; /* supported by Chrome and Opera */  
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    .axes {
    padding: 1em;
    user-select: none; /* supported by Chrome and Opera */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    .buttons {
    margin-left: 1em;
    user-select: none; /* supported by Chrome and Opera */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    /*meter*/.axis {
    min-width: 200px;
    margin: 1em;
    user-select: none; /* supported by Chrome and Opera */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    .button {
    display: inline-block;
    width: 1em;
    text-align: center;
    padding: 1em;
    border-radius: 20px;
    border: 1px solid black;
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAAAAxJREFUCNdjYPjPAAACAgEAqiqeJwAAAABJRU5ErkJggg==);
    background-size: 0% 0%;
    background-position: 50% 50%;
    background-repeat: no-repeat;
    user-select: none; /* supported by Chrome and Opera */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    .pressed {
    border: 1px solid red;
    user-select: none; /* supported by Chrome and Opera */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    .touched::after {
    content: "touch";
    display: block;
    position: absolute;
    margin-top: -0.2em;
    margin-left: -0.5em;
    font-size: 0.8em;
    opacity: 0.7;
    user-select: none; /* supported by Chrome and Opera */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      cursor: none;
    }

    
    </style>
    </head>
    <div class="disable-select">
    <body>
      <h2 id="start">Press a button on your controller to start</h2>


      <div id="x"></div>
      
      <script>

/*
 * Gamepad API Test
 *
 * To the extent possible under law, the author(s) have dedicated all copyright and related and neighboring rights to this software to the public domain worldwide. This software is distributed without any warranty.
 *
 * You should have received a copy of the CC0 Public Domain Dedication along with this software. If not, see <http://creativecommons.org/publicdomain/zero/1.0/>.
 */

var oldetat;
var etat = "Static";
var init = 0;
var haveEvents = 'GamepadEvent' in window;
var haveWebkitEvents = 'WebKitGamepadEvent' in window;
var controllers = {};
var rAF = window.mozRequestAnimationFrame ||
  window.webkitRequestAnimationFrame ||
  window.requestAnimationFrame;

function connecthandler(e) {
  addgamepad(e.gamepad);
}
function addgamepad(gamepad) {
  controllers[gamepad.index] = gamepad; var d = document.createElement("div");
  d.setAttribute("id", "controller" + gamepad.index);
  var t = document.createElement("h1");
  t.appendChild(document.createTextNode("gamepad: " + gamepad.id));
  d.appendChild(t);
  var b = document.createElement("div");
  b.className = "buttons";
  for (var i=0; i<gamepad.buttons.length; i++) {
    var e = document.createElement("span");
    e.className = "button";
    //e.id = "b" + i;
    e.innerHTML = i;
    b.appendChild(e);
  }
  d.appendChild(b);
  var a = document.createElement("div");
  a.className = "axes";
  for (i=0; i<gamepad.axes.length; i++) {
    e = document.createElement("meter");
    e.className = "axis";
    //e.id = "a" + i;
    e.setAttribute("min", "-2");
    e.setAttribute("max", "2");
    e.setAttribute("value", "0");
    e.innerHTML = i;
    a.appendChild(e);
  }
  d.appendChild(a);
  document.getElementById("start").style.display = "none";
  document.body.appendChild(d);
  rAF(updateStatus);
}

function disconnecthandler(e) {
  removegamepad(e.gamepad);
}

function removegamepad(gamepad) {
  var d = document.getElementById("controller" + gamepad.index);
  document.body.removeChild(d);
  delete controllers[gamepad.index];
}

function updateStatus() {
  scangamepads();
  for (j in controllers) {
    var controller = controllers[j];
    var d = document.getElementById("controller" + j);
    var buttons = d.getElementsByClassName("button");
    for (var i=0; i<controller.buttons.length; i++) {
      var b = buttons[i];
      var val = controller.buttons[i];
      var pressed = val == 1.0;
      var touched = false;
      if (typeof(val) == "object") {
        pressed = val.pressed;
        if ('touched' in val) {
          touched = val.touched;
        }
        val = val.value;
      }
      var pct = Math.round(val * 100) + "%";
      b.style.backgroundSize = pct + " " + pct;
      b.className = "button";
      if (pressed) {
        b.className += " pressed";
      }
      if (touched) {
        b.className += " touched";
      }
    }

    var axes = d.getElementsByClassName("axis");
    var img = document.createElement("img");
    
    for (var i=0; i<controller.axes.length; i++) {
      var a = axes[i];
      
      a.innerHTML = i + ": " + controller.axes[i].toFixed(4);
      var value = controller.axes[i] + 1;
      a.setAttribute("value", value);
      
      // console.log(controller.axes.length);
      // a.setAttribute("value", 1);

  
        


      if(controller.axes[5] > 0.50){
        etat="Arriere";
      }
      else if(controller.axes[5] < -0.50){
        etat="Avant";
      }
      else{
        etat="Static";
      }
      
      switch (etat) {
        case 'Static':
          
          if(etat != oldetat){
            img.src = "images/Static.png";
            var div = document.getElementById("x");
            oldetat = "Static";
            if(init == 0){
              div.appendChild(img);
              init = 1;
            }else{
              div.removeChild(div.childNodes[0]);
              div.appendChild(img);
            }
          }
          break;

        case 'Avant':
          if(etat != oldetat){
            img.src = "images/Avant.png";
            var div = document.getElementById("x");
            oldetat = "Avant";
            div.removeChild(div.childNodes[0]);
            div.appendChild(img);
          }
          break;

        case 'Arriere':
          if(etat != oldetat){
            img.src = "images/Arriere.png";
            var div = document.getElementById("x");
            oldetat = "Arriere";
            div.removeChild(div.childNodes[0]);
            div.appendChild(img);
          }
          break;

        default:
        
          break;
      }     
    }
  }
  rAF(updateStatus);
  // console.log(state); //  1/0/-1
  }
// console.log(updateStatus());

function scangamepads() {
  var gamepads = navigator.getGamepads ? navigator.getGamepads() : (navigator.webkitGetGamepads ? navigator.webkitGetGamepads() : []);
  for (var i = 0; i < gamepads.length; i++) {
    if (gamepads[i] && (gamepads[i].index in controllers)) {
      controllers[gamepads[i].index] = gamepads[i];
    }
  }
}

if (haveEvents) {
  
  window.addEventListener("gamepadconnected", connecthandler);
  window.addEventListener("gamepaddisconnected", disconnecthandler);
} else if (haveWebkitEvents) {
  
  window.addEventListener("webkitgamepadconnected", connecthandler);
  window.addEventListener("webkitgamepaddisconnected", disconnecthandler);
} else {
  setInterval(scangamepads, 500);
}
    </script>
    </div>
      </body>
    </html>
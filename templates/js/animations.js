var sections = document.getElementsByClassName("section")
sections[0].classList.add("snap")
document.getElementsByClassName("main")[0].scrollTop = 0;


//start functions
SvgStroke(".logoLines")
setTimeout(function(){
  Popup(".logoFull")
  setTimeout(function(){
    Oppacity(".welcometxt")
    setTimeout(function(){
      sections[1].classList.add('snap')
      sections[2].classList.add('snap')
      Oppacity(".scrolldown-wrapper")
    }, 500);
  }, 500);
}, 1000);

















// animations
function SvgStroke(DataObject){
  anime({
    targets: DataObject,
    strokeDashoffset: [anime.setDashoffset, '5px'],
    duration: 500,
  });
}

function Popup(DataObject){
  anime({
    targets: DataObject,
    scale: 1.0,
    duration: 1000,
  });
}

function Oppacity(DataObject){
  anime({
    targets: DataObject,
    opacity: 1,
    duration: 1000,
  });
}
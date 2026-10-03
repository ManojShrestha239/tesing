function myfunction(){

    var divelement = document.createElement('div')
    divelement.className = "form-group row div-container";
    divelement.id = "div";

    var label = document.createElement('label');
    label.className = "col-md-12 col-form-label";
    label.innerHTML = "Sub-Area";

    var inputdiv = document.createElement('div')
    inputdiv.className = "col-md-5";

    var input = document.createElement('input');
    input.className = "form-control";
    input.setAttribute('required','');
    input.setAttribute('name','subarea[]'); 
    input.setAttribute('value',''); 

    var i = document.createElement('a');
    i.innerHTML = "Remove";
    i.style.background = '#EFEFEF';
    i.className = "btn center col-md-1 remove";
    

    inputdiv.appendChild(input);
    divelement.appendChild(label);
    divelement.appendChild(inputdiv)
    divelement.appendChild(i);

    var divclass = document.getElementById('sub-area-container');
    divclass.insertBefore(divelement,divclass.childNodes[0]);


    
let dd = document.querySelectorAll('.remove');
dd.forEach((check) => {
    check.addEventListener('click', checkIndex);
  })
}
function checkIndex(e){
  console.log(e)
  e.srcElement.parentNode.remove();
}






  // if(dd.length-1==d){
      // divc[d].remove();
              // divc[d].removeChild(divc[d]);
          // }
            // var d = Array.from(dd).indexOf(event.target) ;
            // const dd = document.getElementsByClassName('remove');
// Array.from(dd).forEach(element => {
//     element.addEventListener('click', function() {
//         console.log('data-wow value is: ' + element.dataset.remove);
//         divc[element.dataset.remove].remove();
//       });
//     });
// for (var i = 0; i < dd.length; i++) {
//     dd[i].addEventListener('click', removefunction, false);
// }
// var removefunction = function() {
//     var attribute = this.getAttribute("form-control");
//     alert(attribute);
// };
    // for (var i = 0; i < dd.length; i++) {    
    //     dd[i].addEventListener('click', ((j) => {         
    //     return function() {
    //         divc[j].remove();
    //         // if(dd.length-1==j){
    //         // divc[j].parentNode.removeChild(divc[j]);
    //         // }
    //         // alert(divc[j].className[2]);
    //         // console.log(divc[j].className[2])
    //     //   removefunction()
    //     }
    //   })(i))
    //   // break;
    // }
    // Array.from(dd).forEach(element => {
//     element.addEventListener('click', function() {
//         console.log('data-wow value is: ' + element.dataset.remove);
//       });
//     });
    // break;
    // const divc = document.getElementsByClassName('div-container');
// for (var i = 0; i < dd.length; i++) {
//     dd[i].addEventListener('click', function(i) {
//       //  if(i == dd.length-1){
//            divc[i].remove();
//            console.log(i)
//       //  }
       
//     }.bind(null, i));
//  }
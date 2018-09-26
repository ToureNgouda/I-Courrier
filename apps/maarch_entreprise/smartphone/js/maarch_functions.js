function addNotes (
    fieldNotes,
    coll_id,
    id
)
{

    var empty = false;
    if (fieldNotes.length < 1 || coll_id.length < 1 || id.length < 1) {
        empty = true;
    }
    
    if (empty) {
        alert('Vous devez entrer une note');
        return;
    }
    
    var path_manage_script = 'ajax_add_notes.php';
    console.log(fieldNotes);
    console.log(coll_id);
    console.log(id);
    new Ajax.Request(path_manage_script,
        {
            method:'post',
            parameters: { id : id,
                          coll_id : coll_id,
                          fieldNotes : fieldNotes
                        },
            onSuccess: function(answer){
                eval("response = "+answer.responseText);
                if (response.status == 1) {
                    $('newNote').style.display = 'block';
                    $('newNote').replace(response.newNote);
                    $('fieldNotes').setValue('');
                    $('noNotes').replace('');
                } else {
                    alert(response.msg);
                }
            }
        }
    );
}

function delNotes (
    id
)
{
    var test = confirm('Supprimer ?');
    if (!test) {
        return;
    }
    
    var delDiv = ''+id+'';
    
    var path_manage_script = 'ajax_del_notes.php';

    new Ajax.Request(path_manage_script,
        {
            method:'post',
            parameters: { id : id },
            onSuccess: function(answer){
                eval("response = "+answer.responseText);
                if (response.status == 1) {
                    console.log($(delDiv));
                    $(delDiv).replace('');
                } else {
                    alert(response.msg);
                }
            }
        }
    );
}

function cacher(person, lastname, firstname)
{
    if(document.getElementById(person).style.display == 'none'){
        document.getElementById(person).style.display='block';

    }
    else{
        document.getElementById(person).style.display='none';
    }
    document.getElementById('lastname').value = "";
    document.getElementById('firstname').value = "";
}

function searchContacts(searchValue)
{
    var path_manage_script = 'ajax_contact_list.php';
    new Ajax.Request(path_manage_script,
        {
            method:'post',
            parameters: { searchValue : searchValue },
            onSuccess: function(answer){
                eval("response = "+answer.responseText);
                if (response.status == 1) {
                    $('allContacts').update(response.rechercheContact);
                    $('allContacts').innerHtml;
                    $('noContacts').replace('');
                } else {
                    alert(response.msg);
                }
            }
        }
    );
}

function searchColleagues(searchValue)
{
    var path_manage_script = 'ajax_colleagues_list.php';
    new Ajax.Request(path_manage_script,
        {
            method:'post',
            parameters: { searchValue : searchValue },
            onSuccess: function(answer){
                eval("response = "+answer.responseText);
                if (response.status == 1) {
                    $('allColleagues').update(response.rechercheColleague);
                    $('allColleagues').innerHtml;
                    $('noContacts').replace('');
                } else {
                    alert(response.msg);
                }
            }
        }
    );
}


    function toggle_visibility_suivant(valeur) { 
        
        
        var nombreligneAffiche = document.getElementById('start').value;
        var nblinetoshow = document.getElementById('sendNbLineToShow').value;
        //alert('nblinetoshow :'+nblinetoshow );
        var nombreligneSuivante = document.getElementById('start');
        var hiddenSuivant = parseFloat(nombreligneAffiche) + parseFloat(nblinetoshow);

        if(nombreligneAffiche <= valeur ){
            nombreligneAffiche = parseFloat(nombreligneAffiche) + parseFloat(nblinetoshow);
            nombreligneSuivante.value= parseFloat(nombreligneAffiche);

            id = 'boutonSuivant'; 
            var f = document.getElementById(id);
            if(nombreligneAffiche >=valeur & f.style.display == 'block'){
                document.getElementById(id).setAttribute("disabled","disabled"); 
                   //document.getElementById(id).style.display = 'none';
               }

               id = 'boutonPrecedent'; 
               var f = document.getElementById(id);
               if(f.style.display == 'block'){

           //document.getElementById(id).style.display = 'block';
           document.getElementById(id).removeAttribute("disabled","disabled");
       }
   }


       if(hiddenSuivant >= valeur){
        var nombreligneAffiche2 = nombreligneAffiche -nblinetoshow;
        var iter = nombreligneAffiche2 - nblinetoshow;
        for( iter; iter < nombreligneAffiche2; iter++){
         id = 'res_'+iter;
         var h = document.getElementById(id);
               //alert('iter :'+id);
               if(iter <= nombreligneAffiche2 & h.style.display == 'block'){
                  document.getElementById(id).style.display = 'none';
              }
              
          }   
      }

          var iter = nombreligneAffiche - nblinetoshow;
          for( iter; iter < nombreligneAffiche; iter++){
           id = 'res_'+iter;
           var e = document.getElementById(id);
           if(iter <= nombreligneAffiche & e.style.display == 'none'){
              document.getElementById(id).style.display = 'block';
          }

        }

        var doubleNbLineToShow = nblinetoshow * 2;
        var iter = nombreligneAffiche - doubleNbLineToShow;
        nombreligneAffiche = nombreligneAffiche -nblinetoshow;
        for( iter; iter < nombreligneAffiche; iter++){
           id = 'res_'+iter;
           var e = document.getElementById(id);
           if(iter <= nombreligneAffiche & e.style.display == 'block'){
              document.getElementById(id).style.display = 'none';
          }
          
      }


 }


    function toggle_visibility_precedent(valeur) { 
        var nombreligneAffiche = document.getElementById('start').value;
        var nombreligneSuivante = document.getElementById('start');
        var nblinetoshow = document.getElementById('sendNbLineToShow').value;
        //alert('nblinetoshow :'+nblinetoshow );
        if(nombreligneAffiche > nblinetoshow){
                nombreligneAffiche = parseFloat(nombreligneAffiche) - parseFloat(nblinetoshow);
                nombreligneSuivante.value= parseFloat(nombreligneAffiche);
                id = 'boutonSuivant'; 
                var f = document.getElementById(id);
                if(f.disabled == 'disabled'){
                   document.getElementById(id).removeAttribute("disabled");
                   //document.getElementById(id).style.display = 'block';
                    }

                }

        if(nombreligneAffiche <=nblinetoshow){
                        id = 'boutonPrecedent'; 
                var f = document.getElementById(id);
                if(f.style.display == 'block'){
                   document.getElementById(id).setAttribute("disabled","disabled"); 
                   //document.getElementById(id).style.display = 'none';
                    }
                }

          id = 'boutonSuivant'; 
          var f = document.getElementById(id);
          if(f.style.display == 'block'){

           //document.getElementById(id).style.display = 'block';
           document.getElementById(id).removeAttribute("disabled","disabled");
       }

    var iter = nombreligneAffiche - nblinetoshow;


        for( iter; iter <= nombreligneAffiche; iter++){
         id = 'res_'+iter;
         var e = document.getElementById(id);
         if(iter <= nombreligneAffiche & e.style.display == 'none'){
          document.getElementById(id).style.display = 'block';
        }
       
        }


            iter = nombreligneAffiche + parseFloat(nblinetoshow);
    //alert('iter '+iter);        
    var nombreLigneInferieur = parseFloat(nombreligneAffiche) /*- parseFloat(5)*/;
   for( nombreLigneInferieur; nombreLigneInferieur <= iter; nombreLigneInferieur++){
       id = 'res_'+nombreLigneInferieur;
       var e = document.getElementById(id);
       if(e.style.display == 'block'){
          document.getElementById(id).style.display = 'none';
            }
        }


}

function clean(){
    if(document.getElementById('notes')){
        document.getElementById('notes').parentNode.removeChild(document.getElementById('notes'));
    }
    else if(document.getElementById('signature_recap')){ 
          document.getElementById('signature_recap').parentNode.removeChild(document.getElementById('signature_recap'));
    }
    else if(document.getElementById('sign_main_panel')){ 
          document.getElementById('sign_main_panel').parentNode.removeChild(document.getElementById('sign_main_panel'));
    }
    else if(document.getElementById('details')){
        document.getElementById('details').parentNode.removeChild(document.getElementById('details'));
    }    
    else if(document.getElementById('list_ans')){ 
           document.getElementById('list_ans').parentNode.removeChild(document.getElementById('list_ans'));
           if(document.getElementById('list').style.webkitTransform == 'translateX(-100%)'){
              //alert('-webkit-transform: translateX(-100%)');
              document.getElementById('list').style.webkitTransform = 'translateX(0%)';
              var h1 = document.getElementById('list');
              var att = document.createAttribute("selected");
              att.value = "true";
              h1.setAttributeNode(att);
            }
    }
    else if(document.getElementById('list')){ 
     document.getElementById('list').parentNode.removeChild(document.getElementById('list'));
     if(document.getElementById('__1__').style.webkitTransform == 'translateX(-100%)'){
        //alert('-webkit-transform: translateX(-100%)');
        document.getElementById('__1__').style.webkitTransform = 'translateX(0%)';
        var h1 = document.getElementById('__1__');
        var att = document.createAttribute("selected");
        att.value = "true";
        h1.setAttributeNode(att);
      }
    } else if(document.getElementById('search')){
         //alert('ok search');
            document.getElementById('search').parentNode.removeChild(document.getElementById('search'));
            window.location.reload();
    }
    else if(document.getElementById('about')){
      if(document.getElementById('list')){
        //alert('ok LIST');
          document.getElementById('list').parentNode.removeChild(document.getElementById('list'));
      }
      if(document.getElementById('about')){
        //alert('ok about');
          document.getElementById('about').parentNode.removeChild(document.getElementById('about'));
      }
      if(document.getElementById('search').style.webkitTransform == 'translateX(-100%)'){
       // alert('-webkit-transform: translateX(-100%)');
        document.getElementById('search').style.webkitTransform = 'translateX(0%)';
        var h1 = document.getElementById('search');
        var att = document.createAttribute("selected");
        att.value = "true";
        h1.setAttributeNode(att);
      }
    }else if(document.getElementById('__1__')){
            window.location.reload();
    }
}

function replaceFrameResize(){
  var frameThumb = document.getElementById('frameThumb');
  if (frameThumb){
    var cur_frm = document.getElementById('ifrm');
    var link = cur_frm.src;
    var ifrm = document.createElement("IFRAME");
    ifrm.setAttribute("src", link);
    ifrm.setAttribute("frameborder", "0");
    ifrm.setAttribute("id", "ifrm");
    ifrm.setAttribute("scrolling", "no");
    
    ifrm.style.height = window.innerHeight - 120 + "px";
    ifrm.style.width = window.innerWidth - 5 + "px";
    frameThumb.appendChild(ifrm);
    frameThumb.removeChild(cur_frm);
  }
}

function switchFrame(path, res_id, id_ans){
  var frameThumb = document.getElementById('frameThumb');
  if (frameThumb){
    var cur_frm = document.getElementById('ifrm');

    var cur_show = document.getElementById('type_doc_show').value;
    if (cur_show == 'attach'){
      var link = path+'&res_id='+res_id+'&coll_id=letterbox_coll';
      document.getElementById('type_doc_show').value = 'doc';
    }
    else{
      var link = path+'&res_id='+res_id+'&coll_id=letterbox_coll&res_id_attach='+id_ans;
      document.getElementById('type_doc_show').value = 'attach';
    }
    
    var ifrm = document.createElement("IFRAME");
    ifrm.setAttribute("src", link);
    ifrm.setAttribute("frameborder", "0");
    ifrm.setAttribute("id", "ifrm");
    ifrm.setAttribute("scrolling", "no");
    
    ifrm.style.height = window.innerHeight - 120 + "px";
    ifrm.style.width = window.innerWidth - 5 + "px";
    frameThumb.appendChild(ifrm);
    frameThumb.removeChild(cur_frm);
  }
}

var signaturePad;
var canvas;
// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
}

function loadImgSign(img){
  signaturePad.clear();
  signaturePad.fromDataURL(img.src);
}
function loadSignPad(){   
    var wrapper = document.getElementById("signature-pad");
    canvas = wrapper.querySelector("canvas");
    var clearButton = wrapper.querySelector("[data-action=clearBut]"),
      addButton = wrapper.querySelector("[data-action=addBut]"),
      saveButton = wrapper.querySelector("[data-action=saveBut]"),
      redPenBut = wrapper.querySelector("[data-action=redPen]"),
      bluePenBut = wrapper.querySelector("[data-action=bluePen]"),
      greenPenBut = wrapper.querySelector("[data-action=greenPen]"),
      blackPenBut = wrapper.querySelector("[data-action=blackPen]"),

      smallPenBut = wrapper.querySelector("[data-action=smallPen]"),
      midPenBut = wrapper.querySelector("[data-action=midPen]"),
      bigPenBut = wrapper.querySelector("[data-action=bigPen]");

      //stampBut = wrapper.querySelector("[data-action=stampBut]"),

    window.onresize = resizeCanvas;
    resizeCanvas();

    signaturePad = new SignaturePad(canvas);
    signaturePad.stampImg = '';


    redPenBut.addEventListener("click", function (event) {
      signaturePad.penColor = 'red';
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.remove('colBlue');
        elements[i].classList.remove('colGreen');
        elements[i].classList.remove('colBlack');
        elements[i].classList.add('colRed');
      }
      redPenBut.classList.add('selected_but');
      bluePenBut.classList.remove('selected_but');
      greenPenBut.classList.remove('selected_but');
      blackPenBut.classList.remove('selected_but');
    });

    bluePenBut.addEventListener("click", function (event) {
      signaturePad.penColor = 'blue';
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.add('colBlue');
        elements[i].classList.remove('colGreen');
        elements[i].classList.remove('colBlack');
        elements[i].classList.remove('colRed');
      }

      bluePenBut.classList.add('selected_but');
      redPenBut.classList.remove('selected_but');
      greenPenBut.classList.remove('selected_but');
      blackPenBut.classList.remove('selected_but');

    });
    greenPenBut.addEventListener("click", function (event) {
      signaturePad.penColor = 'green';
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.remove('colBlue');
        elements[i].classList.add('colGreen');
        elements[i].classList.remove('colBlack');
        elements[i].classList.remove('colRed');
      }
      greenPenBut.classList.add('selected_but');
      redPenBut.classList.remove('selected_but');
      bluePenBut.classList.remove('selected_but');
      blackPenBut.classList.remove('selected_but');

    });
    blackPenBut.addEventListener("click", function (event) {
      signaturePad.penColor = 'black';
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.remove('colBlue');
        elements[i].classList.remove('colGreen');
        elements[i].classList.add('colBlack');
        elements[i].classList.remove('colRed');
      }

      blackPenBut.classList.add('selected_but');
      redPenBut.classList.remove('selected_but');
      bluePenBut.classList.remove('selected_but');
      greenPenBut.classList.remove('selected_but');

    });

    smallPenBut.addEventListener("click", function (event) {
      signaturePad.maxWidth = 3;
      signaturePad.minWidth = 0.7;
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.remove('selected_but');
      }
      smallPenBut.classList.add('selected_but');
    });
    midPenBut.addEventListener("click", function (event) {
      signaturePad.maxWidth = 6;
      signaturePad.minWidth = 2;
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.remove('selected_but');
      }
      midPenBut.classList.add('selected_but');
    });
    bigPenBut.addEventListener("click", function (event) {
      signaturePad.maxWidth = 10;
      signaturePad.minWidth = 3;
      var elements = document.getElementsByClassName("sizeBut");
      for(var i=0, l=elements.length; i<l; i++){
        elements[i].classList.remove('selected_but');
      }
      bigPenBut.classList.add('selected_but');
    });



  clearButton.addEventListener("click", function (event) {
      signaturePad.clear();
      signaturePad.stampImg = '';
  });

  /*stampBut.addEventListener("click", function (event) {
      if (signaturePad.stampImg != ''){
        signaturePad.stampImg = '';
        stampBut.classList.remove('selected_but');
      }
      else{
        signaturePad.stampImg = document.getElementById("imgStamp").src;
        stampBut.classList.add('selected_but');
      }
  });*/

  saveButton.addEventListener("click", function (event) {
      if (signaturePad.isEmpty()) {
          alert("Vous devez signer avant de valider.");
      } else {
        
        document.getElementById("loading_sign").style.display = 'inline';
        var data_img = signaturePad.toDataURL();

        var path_manage_script = 'sign_file_rep.php';
        new Ajax.Request(path_manage_script,
            {
                method:'post',
                parameters: { 'imageData' : data_img, 'res_id' : document.getElementById("res_id_master").value, 'res_id_attach' : document.getElementById("res_id_attach").value },
                onSuccess: function(answer){
                  document.getElementById("loading_sign").style.display = 'none';
                  eval("response = "+answer.responseText);
                  if (response.status == 1) {
                    document.getElementById("link_recap").click();
                  }
                  else if (response.status == 0) {
                    document.getElementById("link_check_user").click();
                  }
                }
            }
        );
      }
  });

}

function loadDeviceInfos() {
 var fp2 = new Fingerprint2();
    fp2.get(function(result, components) {
        for (var index in components) {
            var obj = components[index];
            var value = obj.value;
            if(typeof value !== "undefined") {
              var line = obj.key + " = " + value.toString().substr(0, 100);
              details += line + '\n';
            }
          }
        document.getElementById("fp").value = result;
        document.getElementById("details").value = details;
    });
}

function valid_sign(res_id){
  var path_manage_script = 'valid_sign.php';
  new Ajax.Request(path_manage_script,
      {
          method:'post',
          parameters: { 'res_id' : res_id, 'code_session' : document.getElementById("code_session").value },
          onSuccess: function(answer){
            eval("response = "+answer.responseText);
            if (response.status == 1) {
              document.getElementById("link_recap").click();
            }
            else if (response.status == 0) {
             console.log('Erreur de validation');
            }
          }
      }
  );
}

function save_sign(){
  var path_manage_script = 'saveSign.php';
  new Ajax.Request(path_manage_script,
      {
          method:'post',
          onSuccess: function(answer){
            eval("response = "+answer.responseText);
            if (response.status == 1){
              document.getElementById("linkSaveSign").style.color = 'green';
              document.getElementById("linkSaveSign").removeAttribute("onclick"); 
            }
          }
      }
  );
}



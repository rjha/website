

String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        });
};


/* JSON support for old browsers */
/* also see  https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/JSON  */

if (!window.JSON) {
    console.log("Old browser using imitation of native JSON object");
    window.JSON = {
        parse: function (sJSON) {return eval("(" + sJSON + ")");},
        stringify: function (vContent) {
            if (vContent instanceof Object) {
                var sOutput = "";
                if (vContent.constructor === Array) {
                    for (var nId = 0; nId < vContent.length; sOutput += this.stringify(vContent[nId]) + ",", nId++);
                    return "[" + sOutput.substr(0, sOutput.length - 1) + "]";
                }

                if (vContent.toString !== Object.prototype.toString) {
                    return "\"" + vContent.toString().replace(/"/g, "\\$&") + "\"";
                }
                for (var sProp in vContent) {
                    sOutput += "\"" + sProp.replace(/"/g, "\\$&") + "\":" + this.stringify(vContent[sProp]) + ",";
                }
                return "{" + sOutput.substr(0, sOutput.length - 1) + "}";
          }
          return typeof vContent === "string" ? "\"" + vContent.replace(/"/g, "\\$&") + "\"" : String(vContent);
        }
  };
}


/* + namepsaces */
webgloo = window.webgloo || {};
webgloo.wb = webgloo.wb || {};

webgloo.message = {
    get : function(key,data) {
        var buffer = '' ;
        if(webgloo.message.hasOwnProperty(key)) {
            buffer = webgloo.message[key].supplant(data);
        }

        return buffer ;
    }
} 

webgloo.message.SPINNER = '<div> <img src="/css/asset/fs/fb_loader.gif" alt="spinner"/></div>' ;
webgloo.message.IS_REQUIRED = 'This is required!' ;


/* +ajax wrapper */
webgloo.Ajax = {
     
    addSpinner : function(messageDivId) {
        $(messageDivId).html('');
        var content = webgloo.message.SPINNER ;
        $(messageDivId).html(content);
       
    },

    show: function (messageDivId,content) {
        $(messageDivId).html(content);
    },

    post:function (dataObj,options) {

        /* @imp define all properties that we wish to override */
        var defaults = {
            type : "POST",
            dataType : "json",
            timeout : 9000,
            onDoneHandler : undefined 
        }

        var settings = $.extend({}, defaults, options);
        this.addSpinner(settings.messageDivId);
        

        var xmlRequest = $.ajax({
            url: dataObj.endPoint,
            type: settings.type ,
            dataType: settings.dataType,
            data :  dataObj.params,
            timeout: settings.timeout,
            processData:true
        }) ;

        xmlRequest.fail(function(response) {
            webgloo.Ajax.show(settings.messageDivId,response);
        });

        xmlRequest.done(function(response) {
            
            if(settings.dataType == 'json') {
                webgloo.Ajax.show(settings.messageDivId,response.message);
            }

            if(typeof settings.onDoneHandler !== "undefined") {
                settings.onDoneHandler(dataObj,response);
            }
        }) ;
        
    }


}

/* + webgloo media object */

webgloo.media = {
    images : {} ,
    debug : false,

    init : function (options) {
         /* @imp define all properties that we wish to override */
        var defaults = {
            formName : "form1",
            formId : "#form1",
            removeImageClass : "a.remove-image",
            previewDiv : "#image-preview",
            imageDiv : '<div class="container" id="image-{id}"> '
                + ' <img src="{srcImage}" alt="{originalName}" width="{width}" height="{height}"/> '
                + '<div class="link"> <a class="remove-image" id="{id}" href="">Remove</a> </div> </div>'

            
        };

        webgloo.media.settings = $.extend({}, defaults, options);

        frm = document.forms[webgloo.media.settings.formName];
        var strImagesJson = frm.media_json.value ;
        var images = JSON.parse(strImagesJson);
        for(i = 0 ;i < images.length ; i++) {
            webgloo.media.addImage(images[i]);
        }

    },

    attachEvents : function() {
        $(webgloo.media.settings.removeImageClass).live("click", function(event){
            event.preventDefault();
            webgloo.media.removeImage($(this));
        }) ;

        $(webgloo.media.settings.formId).submit(function() {
            webgloo.media.populateHidden();
            return true;
        });

    },

    populateHidden : function () {

        var frm = document.forms[webgloo.media.settings.formName];
        var images = new Array() ;
        $(webgloo.media.settings.previewDiv).find('a').each(function(index) {
             var imageId = $(this).attr("id");
             images.push(webgloo.media.images[imageId]);
        });

        var strImages =  JSON.stringify(images);
        frm.media_json.value = strImages ;
    },

    removeImage : function(linkObj) {
        var id = $(linkObj).attr("id");
        var imageId = "#image-" +id ;
        $("#image-"+id).remove();
    },

    addImage : function(mediaVO) {
        
        webgloo.media.images[mediaVO.id] = mediaVO ;

        switch(mediaVO.store) {

            case "s3" :
                mediaVO.srcImage = 'http://' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
                var buffer = webgloo.media.settings.imageDiv.supplant(mediaVO);
                $(webgloo.media.settings.previewDiv).append(buffer);
                break ;

            case "local" :
                mediaVO.srcImage = '/' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
                var buffer = webgloo.media.settings.imageDiv.supplant(mediaVO);
                $(webgloo.media.settings.previewDiv).append(buffer);
                break ;

            default:
                break ;
        }
        
    }
}




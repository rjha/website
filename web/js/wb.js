

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
webgloo.fs = webgloo.fs || {};

webgloo.fs.message = {
    get : function(key,data) {
        var buffer = '' ;
        if(webgloo.fs.message.hasOwnProperty(key)) {
            buffer = webgloo.fs.message[key].supplant(data);
        }

        return buffer ;
    }
} 

webgloo.fs.message.SPINNER = '<div> <img src="/css/asset/fs/fb_loader.gif" alt="spinner"/></div>' ;
webgloo.fs.message.IS_REQUIRED = 'This is required!' ;
webgloo.fs.message.COURIER_REQUIRED = '<span class="error">courier information is required! </span>' ;

webgloo.fs.Ajax = {
     
    addSpinner : function(messageDivId) {
        $(messageDivId).html('');
        var content = webgloo.fs.message.SPINNER ;
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
            webgloo.fs.Ajax.show(settings.messageDivId,response);
        });

        xmlRequest.done(function(response) {
            
            if(settings.dataType == 'json') {
                webgloo.fs.Ajax.show(settings.messageDivId,response.message);
            }

            if(typeof settings.onDoneHandler !== "undefined") {
                settings.onDoneHandler(dataObj,response);
            }
        }) ;
        
    }


}

/* jquery validator object for order page */

// Rules
// first name/ last name - min :3 max 30 | alphanumeric
// first name <> last name
// email : 64/ valid format
// phone : digits only / max 16
// address : 100 / min 6
// city : 3-30 chars | alphabets only
// state : required
// pincode 2-12 : numbers only


webgloo.fs.OrderValidator = 
{
    errorLabelContainer: $("#form-message"),
    onkeyup:false,
    
    rules: {
        first_name: {required: true, maxlength:30, minlength:3} ,
        last_name : { required : true, maxlength : 30, minlength :2},
        email: {required: true, email:true } ,
        phone : {required : true, digits : true},
        billing_address: {required: true , maxlength:100, minlength : 6} ,
        billing_city: {required: true , maxlength:30, minlength:3} ,
        billing_state: {required: true} ,
        billing_pincode: {required: true , maxlength:12, minlength:2} ,

        ship_first_name: {required: true, maxlength:30, minlength:3} ,
        ship_last_name : { required : true, maxlength : 30, minlength :2},
        ship_address: {required: true , maxlength:100, minlength : 6} ,
        ship_city: {required: true , maxlength:30, minlength:3} ,
        ship_state: {required: true} ,
        ship_pincode: {required: true , maxlength:12, minlength:2}, 
        ship_phone : {required : true, digits : true}
        
    },
    messages: {
        first_name: {
            required: "First name is required ", 
            maxlength : "Only 30 chars allowed in First Name", 
            minlength: "At least 3 chars required in First Name"
        } ,
        last_name : { 
            required : "Last name is required ",
            maxlength : "Only 30 chars allowed in Last Name", 
            minlength: "At least 2 chars required in Last Name"
        },
        ship_first_name: {
            required: "First name (shipping) is required ", 
            maxlength : "Only 30 chars allowed in First Name", 
            minlength: "At least 3 chars required in First Name"
        } ,
        ship_last_name : { 
            required : "Last name (shipping) is required ",
            maxlength : "Only 30 chars allowed in Last Name", 
            minlength: "At least 2 chars required in Last Name"
        },
        email: {
            required: "Email is required", 
            email : "Email is not in valid format" ,
        } ,
        phone : {
            required : "Phone is required", 
            digits : "Only numbers are allowed in Phone"
        },
        billing_address: {
            required: "Address (billing) is required " , 
            maxlength:"Only 100 chars allowed in Address",
            minlength: "At least 6 chars required in  Address"
        } ,
        billing_city: {
            required: true , 
            maxlength : "Only 30 chars allowed in City (billing)", 
            minlength: "At least 3 chars required in City(billing)"
        } ,
        billing_state: {
            required: "State (billing) is required"
        } ,
        billing_pincode: {
            required: "Pincode (billing) is required " , 
            maxlength:"Only 12 chars allowed in Pincode (billing)",
            minlength: "Atleast 2 chars required in Pincode (billing)"
        },

        ship_address: {
            required: "Address (shipping) is required " , 
            maxlength:"Only 100 chars allowed in Address",
            minlength: "At least 6 chars required in  Address"
        } ,
        ship_city: {
            required: true , 
            maxlength : "Only 30 chars allowed in City (shipping)", 
            minlength: "At least 3 chars required in City(shipping)"
        } ,
        ship_state: {
            required: "State (shipping) is required"
        } ,
        ship_pincode: {
            required: "Pincode (shipping) is required " , 
            maxlength:"Only 12 chars allowed in Pincode (shipping)",
            minlength: "Atleast 2 chars required in Pincode (shipping)"
        },
        ship_phone : {
            required : "Phone (shipping) is required", 
            digits : "Only numbers are allowed in Phone (shipping)"
        }
    }
}


webgloo.fs.invoice = {

    mail : function(invoiceId,callback) {
        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.invoiceId  = invoiceId;
        dataObj.endPoint = "/app/action/invoice/ajax/mail.php";
        
        var options = {
            "dataType" : "json", 
            "timeout" : 9000,
            "messageDivId" : "#invoice-ajax-" + invoiceId ,
            onDoneHandler : callback.mail
        };
        
        webgloo.fs.Ajax.post(dataObj,options) ;
    },

    edit : function (invoiceId,callback) {
        // nothing to do online
        var dataObj = {} ;
        var response = {} ;
        dataObj.params = {} ;
        dataObj.params.invoiceId = invoiceId ;
        callback.edit(dataObj,response);
    },

    cancel : function(invoiceId,callback) {
        $("#invoice-ajax-" + invoiceId).html("Not implemented yet");
    },

    shipping : function (invoiceId,callback) {
        
        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.invoiceId  = invoiceId;
        dataObj.endPoint = "/app/invoice/get-shipping.php";
        
        var options = {
            "dataType" : "text", 
            "timeout" : 9000,
            "messageDivId" : "#invoice-ajax-" + invoiceId ,
            onDoneHandler : callback.shipping
        };
        
        webgloo.fs.Ajax.post(dataObj,options) ;
    },

    remind : function(invoiceId,callback) {
        $("#invoice-ajax-" + invoiceId).html("Not implemented yet");
    },

    initActions : function(callback) {
        this.callback = callback ;

        $("a.invoice-action").live("click",function(event){
            event.preventDefault();
            var invoiceId = $(this).attr("id");
            var action =  $(this).attr("rel") ;

            switch(action) {
                case 'mail' :
                    webgloo.fs.invoice.mail(invoiceId,callback);
                    break ;
                case 'edit' :
                    webgloo.fs.invoice.edit(invoiceId,callback);
                    break ;
                case 'cancel' :
                    webgloo.fs.invoice.cancel(invoiceId,callback);
                    break ;
                case 'shipping' :
                    webgloo.fs.invoice.shipping(invoiceId,callback);
                    break ;
                case 'remind' :
                    webgloo.fs.invoice.remind(invoiceId,callback);
                    break ;
                
                default :
                    break ;

            }
 
        }) ;
    }

}



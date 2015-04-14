$.fn.alertWindow = function(title, message, type, errors) {
    if (!type) type = 'danger';
    errors = errors || [];
    var list = "";
    if(errors && errors.length) {
        list += "<ul>";
        var length = errors.length;
        for(var i = 0; i < length; i++) {
            var error = errors[i];
            list += "<li>" + error + "</li>";
        }
        list += "</ul>";
    }
    var html = '<div class="alert alert-' + type + '"><a class="close" data-dismiss="alert">&times;</a><span>' + (title !== "" ? "<h4>" + title + "</h4>" : "") + message + '' + list + '</span></div>';

    $(this).html(html).fadeIn();

    return this;
};

$.fn.clearFormFields = function() {
    $(this).find('input[type="text"],input[type="email"],textarea,select').val('').removeAttr('disabled');
};

$.fn.serializeFormData = function() {
    var fd = new FormData();
    var $form = $(this);
    $form.find('input[type="file"]').each(function() {
        var $this = $(this);
        var file_data = $this[0].files;
        if(!file_data) return;
        var multiple = file_data.length > 1 ? '[]' : '';
        for(var i = 0; i<file_data.length; i++){
            fd.append($this.attr('name') + multiple, file_data[i]);
        }
    });
    var other_data = $form.serializeArray();
    $.each(other_data,function(key,input){
        fd.append(input.name,input.value);
    });

    return fd;
}
$.fn.exists = function(){return this.length>0;}
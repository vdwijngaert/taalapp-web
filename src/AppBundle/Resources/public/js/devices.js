(function($) {
    var $deviceModal = $("#deviceModal");

    if(!$deviceModal.length) return;

    var Device = function() {
        this.$deviceModal = $deviceModal;
        this.$deviceList = $("#device-list");

        this.init();
    };

    Device.prototype = {
        $deviceModal: null,
        $deviceList: null,
        init: function() {
            this.eventListeners();
        },
        eventListeners: function() {
            var self = this;

            this.$deviceModal.on('click', '.save-changes', function(e) {
                e.preventDefault();

                self.addDevice();
            });

            this.$deviceList.on('click', 'a.removeBtn', function(e) {
                e.preventDefault();

                var device = $(this).closest('tr').attr('data-device-id');

                if(!device) return;

                self.removeDevice(device);
            });
        },
        addDevice: function() {
            var self = this,
                $form = this.$deviceModal.find("form");

            $.ajax({
                url: FRONT.baseUrl + '/devices/add',
                method: 'POST',
                data: $form.serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if (data.id) {
                        self.showDevice(data);
                    } else if (data.title && data.message) {
                        self.$deviceModal.find(".placeholder").alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$deviceModal.find(".placeholder").alertWindow("Fout", "Kon apparaat niet toevoegen.");
                },
                complete: function () {
                }
            });
        },
        showDevice: function(device) {
            this.$deviceModal.find("input,button,textarea,select").prop("disabled", true);

            this.$deviceModal.find(".modal-body").html(
                '<h2>Gelukt!</h2>' +
                '<p>Je hebt het apparaat "' + device.name + '" succesvol toegevoegd.</p>' +
                '<h3>App koppelen</h3>' +
                '<p>Voer volgende stappen uit om je apparaat te koppelen:</p>' +
                '<p><ol>' +
                    '<li>Open de app op je apparaat</li>' +
                    '<li>Vul je gebruikersnaam in: <code class="username">' + FRONT.userName + '</code></li>' +
                    '<li>Vul je unieke toegangscode in: <code class="username">' + device.token + '</code></li>' +
                    '<li>Bevestig met een druk op de knop</li>' +
                '</ol></p><p>&nbsp;</p>' +
                '<p><a href="javascript:location.reload();" class="btn btn-block btn-lg btn-success"><span class="glyphicon glyphicon-ok"></span> Ik heb mijn apparaat geactiveerd</a></p>'
            );
        },
        removeDevice: function(deviceId) {
            if(!confirm('Weet u zeker dat u dit apparaat wil verwijderen? U zal op dit apparaat niet meer aan uw vragen kunnen.')) {
                return;
            }

            var self = this;

            $.ajax({
                url: FRONT.baseUrl + '/devices/remove/' + parseInt(deviceId),
                method: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    if (data === true) {
                        window.location.reload();
                    } else if (data.title && data.message) {
                        self.$deviceList.find(".placeholder").alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$deviceList.find(".placeholder").alertWindow("Fout", "Kon apparaat niet toevoegen.");
                },
                complete: function () {
                }
            });
        }
    };

    window.device = new Device();
})(jQuery);
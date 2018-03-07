"use strict";

window.Settings = (function(){

    function Settings(settings) {
        this.settings = {
            ShowAllEW: settings.ShowAllEW || {keyCode: 87, shiftKey: false, altKey: false, ctrlKey: false, metaKey:false },
            ZoomLevelToStrategic: 0.2
        };

        this.set = function (key, value) {
          if (this.settings[key] === undefined)  {
              throw new Error("Unrecognized settings key '" + key + "'");
          }

          console.log("setting key", key, "to", value, typeof value);
          this.settings[key] = value;
        };

        this.save = function () {
            localStorage.setItem("settings", JSON.stringify(this.settings));
        };
    }

    Settings.prototype.matchEvent = function (event) {
        return Object.keys(this.settings).find(function (action) {

            var def = this.settings[action];
            if (!def || !def.keyCode) {
                return false;
            }

            return def.keyCode === event.keyCode
                && def.shiftKey === event.shiftKey
                && def.altKey === event.altKey
                && def.ctrlKey === event.ctrlKey
                && def.metaKey === event.metaKey
        }, this)
    };

    function load(){
        var settings = localStorage.getItem("settings") || "{}";
        return JSON.parse(settings);
    }

    return new Settings(load());
})();
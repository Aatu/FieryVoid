window.DeploymentIcon = (function(){

    function DeploymentIcon(position, size, type, scene)
    {
        this.z = -2;
        this.mesh = null;
        this.size = size;
        this.color = getColorByType(type);
        this.opacity = 0.5;

        this.mesh = new THREE.Object3D();
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
        this.mesh.renderDepth = 10;

        var lineWidth = 10;

        var borders = new window.BoxSprite(size, lineWidth, this.z, this.color, this.opacity);
        this.mesh.add(borders.mesh);

        var plain = new window.PlainSprite({width: size.width+lineWidth, height: size.height+lineWidth}, this.z, this.color, this.opacity * 0.5);
        this.mesh.add(plain.mesh);
        scene.add(this.mesh);
        this.hide();
    }

    DeploymentIcon.prototype.hide = function(){
        this.mesh.visible = false;
    };

    DeploymentIcon.prototype.show = function(){
        this.mesh.visible = true;
    };

    function getColorByType(type){
        if (type == "own") {
            return new THREE.Color(160/255,250/255,100/255);
        } else if (type == "ally") {
            return new THREE.Color(100/255,170/255,250/255);
        } else {
            return new THREE.Color(250/255,100/255,100/255);
        }
    }

    return DeploymentIcon;
})();
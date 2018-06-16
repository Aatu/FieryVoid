<script id="spriteVertexShader" type="x-shader/x-vertex">
    varying vec2 vUv;

    void main() {
        vUv = uv;

        gl_Position =   projectionMatrix *
                        modelViewMatrix *
                        vec4(position,1.0);
    }
</script>

<script id="spriteFragmentShader" type="x-shader/x-fragment">
    uniform sampler2D texture;
    uniform float overlayAlpha;
    uniform vec3 overlayColor;
    uniform float opacity;

    varying vec2 vUv;

    void main() {
        vec4 textureColor = texture2D(texture, vUv);
        vec4 finalColor = textureColor;

        if (overlayAlpha > 0.0 && textureColor.a > 0.0){
            //finalColor = vec4(overlayColor, textureColor.a);
            //finalColor = mix(textureColor, overlayColor, overlayAlpha);
            //finalColor = (1.0 - t1.a) * t0 + t1.a * t1;
            finalColor = (1.0 - overlayAlpha) * textureColor + overlayAlpha * vec4(overlayColor, 1.0);
            finalColor.a = textureColor.a;
        }

        if (finalColor.a > 0.0) {
            finalColor.a *= opacity;
        }

        gl_FragColor = finalColor;

    }
</script>

<script id="boxSpriteVertexShader" type="x-shader/x-vertex">
    varying vec2 vUv;

    void main() {
        vUv = uv;

        gl_Position =   projectionMatrix *
                        modelViewMatrix *
                        vec4(position,1.0);
    }
</script>

<script id="boxSpriteFragmentShader" type="x-shader/x-fragment">
    uniform sampler2D texture;
    uniform vec4 color;

    varying vec2 vUv;

    void main() {
        gl_FragColor = color;
    }
</script>

<script id="effectVertexShader" type="x-shader/x-fragment">
    attribute vec3 color;
    attribute float opacity;
    attribute float fadeInTime;
    attribute float fadeInSpeed;
    attribute float fadeOutTime;
    attribute float fadeOutSpeed;
    attribute float size;
    attribute float sizeChange;
    attribute float angle;
    attribute float angleChange;
    attribute vec3 velocity;
    attribute vec3 acceleration;
    attribute float activationGameTime;
    attribute float textureNumber;
    uniform float zoomLevel;
    uniform float gameTime;
    varying vec4  vColor;
    varying float vAngle;
    varying float textureN;
    void main()
    {
        float elapsedTime = gameTime - activationGameTime;

        if (elapsedTime < 0.0 || (fadeOutTime > 0.0 && gameTime - (fadeOutTime + fadeOutSpeed) > 0.0)) {
            gl_PointSize = 0.0;
            gl_Position = vec4( 0.0, 0.0, 0.0, 1.0 );
            vColor = vec4(0.0, 0.0, 0.0, 0.0);
            textureN = textureNumber;
            return;
        }

        float currentOpacity = 0.0;
        if (fadeInSpeed == 0.0) {
            currentOpacity = opacity;
        }


        if (fadeInSpeed > 0.0 && gameTime > fadeInTime)
        {
            float fadeIn = (gameTime - fadeInTime) / fadeInSpeed;
        if (fadeIn > 1.0) fadeIn = 1.0;
            currentOpacity =  opacity * fadeIn;
        }

        if (fadeOutSpeed > 0.0 && gameTime > fadeOutTime)
        {
            float fadeOut = (gameTime - fadeOutTime) / fadeOutSpeed;
        if (fadeOut > 1.0) fadeOut = 1.0;
            currentOpacity =  currentOpacity * (1.0 - fadeOut);
        }

        if ( currentOpacity > 0.0 && elapsedTime >= 0.0)
        {
            vColor = vec4( color, currentOpacity );
            if (zoomLevel < 0.2) {
                vColor = vec4( vec3(1.0, 1.0, 1.0), currentOpacity );
            }
        } else {
            vColor = vec4(0.0, 0.0, 0.0, 0.0);
        }

        vAngle = angle + angleChange * elapsedTime;
        textureN = textureNumber;

        vec3 displacement = velocity * elapsedTime;
        vec3 accelerationDisplacement  = elapsedTime * elapsedTime * 0.5 * acceleration;

        vec3 modPos = position + displacement + accelerationDisplacement;


        gl_PointSize = clamp(size + (sizeChange * elapsedTime), 0.0, 1024.0) * zoomLevel;
        gl_Position = projectionMatrix * modelViewMatrix * vec4( modPos, 1.0 );
    }
</script>

<script id="effectFragmentShader" type="x-shader/x-fragment">
    uniform sampler2D texture;
    varying vec4 vColor;
    varying float vAngle;
    varying float textureN;
    void main()
    {
        gl_FragColor = vColor;
        if (gl_FragColor.a == 0.0) {
            return;
        }

        float c = cos(vAngle);
        float s = sin(vAngle);
        float textureAmount = 8.0;
        vec2 tPos = vec2((mod(textureN, textureAmount) * (1.0 / textureAmount)), (floor(textureN / textureAmount) * (1.0 / textureAmount)));

        vec2 pos = vec2(gl_PointCoord.x, gl_PointCoord.y);
        vec2 tCen = vec2(0.5, 0.5);

        vec2 rPos = vec2(
            tCen.x + c * (pos.x - tCen.x) - s * (pos.y - tCen.y),
            tCen.y + s * (pos.x - tCen.x) + c * (pos.y - tCen.y)
        );

        rPos = clamp(rPos, 0.0, 1.0);

        vec2 finalPos = vec2(
            (rPos.x / textureAmount + tPos.x),
            1.0 - (rPos.y / textureAmount + tPos.y)
        );

        vec4 rotatedTexture = texture2D(texture, finalPos);
        gl_FragColor = gl_FragColor * rotatedTexture;
    }
</script>


<script id="starVertexShader" type="x-shader/x-fragment">
    attribute vec3 color;
    attribute float opacity;
    attribute float fadeInTime;
    attribute float fadeInSpeed;
    attribute float fadeOutTime;
    attribute float fadeOutSpeed;
    attribute float size;
    attribute float sizeChange;
    attribute float angle;
    attribute float angleChange;
    attribute vec3 velocity;
    attribute vec3 acceleration;
    attribute float activationGameTime;
    attribute float textureNumber;
    attribute float parallaxFactor;
    uniform float gameTime;
    uniform mat4 customMatrix;
    varying vec4  vColor;
    varying float vAngle;
    varying float textureN;
    void main()
    {
        float elapsedTime = gameTime - activationGameTime;

        if (elapsedTime < 0.0 || (fadeOutTime > 0.0 && gameTime - (fadeOutTime + fadeOutSpeed) > 0.0)) {
            gl_PointSize = 0.0;
            gl_Position = vec4( 0.0, 0.0, 0.0, 1.0 );
            vColor = vec4(0.0, 0.0, 0.0, 0.0);
            textureN = textureNumber;
            return;
        }

        float currentOpacity = 0.0;
        if (fadeInSpeed == 0.0) {
            currentOpacity = opacity;
        }


        if (fadeInSpeed > 0.0 && gameTime > fadeInTime)
        {
            float fadeIn = (gameTime - fadeInTime) / fadeInSpeed;
        if (fadeIn > 1.0) fadeIn = 1.0;
            currentOpacity =  opacity * fadeIn;
        }

        if (fadeOutSpeed > 0.0 && gameTime > fadeOutTime)
        {
            float fadeOut = (gameTime - fadeOutTime) / fadeOutSpeed;
        if (fadeOut > 1.0) fadeOut = 1.0;
            currentOpacity =  currentOpacity * (1.0 - fadeOut);
        }

        if ( currentOpacity > 0.0 && elapsedTime >= 0.0)
        {
            vColor = vec4( color, currentOpacity );
        } else {
            vColor = vec4(0.0, 0.0, 0.0, 0.0);
        }

        vAngle = angle + angleChange * elapsedTime;
        textureN = textureNumber;

        vec3 displacement = velocity * elapsedTime;
        vec3 accelerationDisplacement  = elapsedTime * elapsedTime * 0.5 * acceleration;

        vec3 modPos = vec3( position.x - (cameraPosition.x * parallaxFactor), position.y - (cameraPosition.y * parallaxFactor), position.z ) + displacement + accelerationDisplacement;


        gl_PointSize = clamp(size + (sizeChange * elapsedTime), 0.0, 1024.0);
        gl_Position = customMatrix * vec4( modPos, 1.0 );
    }
</script>
<script id="starFragmentShader" type="x-shader/x-fragment">
    uniform sampler2D texture;
    varying vec4 vColor;
    varying float vAngle;
    varying float textureN;
    void main()
    {
        gl_FragColor = vColor;
        if (gl_FragColor.a == 0.0) {
            return;
        }

        float c = cos(vAngle);
        float s = sin(vAngle);
        float textureAmount = 8.0;
        vec2 tPos = vec2((mod(textureN, textureAmount) * (1.0 / textureAmount)), (floor(textureN / textureAmount) * (1.0 / textureAmount)));

        vec2 pos = vec2(gl_PointCoord.x, gl_PointCoord.y);
        vec2 tCen = vec2(0.5, 0.5);

        vec2 rPos = vec2(
            tCen.x + c * (pos.x - tCen.x) - s * (pos.y - tCen.y),
            tCen.y + s * (pos.x - tCen.x) + c * (pos.y - tCen.y)
        );

        rPos = clamp(rPos, 0.0, 1.0);

        vec2 finalPos = vec2(
            (rPos.x / textureAmount + tPos.x),
            1.0 - (rPos.y / textureAmount + tPos.y)
        );

        vec4 rotatedTexture = texture2D(texture, finalPos);
        gl_FragColor = gl_FragColor * rotatedTexture;
    }
</script>

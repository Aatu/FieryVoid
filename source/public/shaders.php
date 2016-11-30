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
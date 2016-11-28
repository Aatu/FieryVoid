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

    varying vec2 vUv;

    void main() {
        gl_FragColor = texture2D(texture, vUv);
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
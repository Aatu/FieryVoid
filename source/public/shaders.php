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
        gl_FragColor = texture2D(texture, vUv); // Displays Nothing
        //gl_FragColor = vec4(0.5, 0.2, 1.0, 1.0); // Works; Displays Flat Color
    }
</script>

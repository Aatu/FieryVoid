// FV THREE global shim — perf roadmap #5 (see project_three_modularize_plan memory).
//
// FV's ~41 legacy client files (plus the THREE.MeshLine extension) reach for a
// global `window.THREE`. The full vendored UMD `three.min.js` (r160, ~670KB) ships
// the entire engine — animation, skinning, audio, WebXR, loaders, post-processing —
// none of which FV uses. This entry imports ONLY the symbols FV actually references
// (verified by grepping all source for `THREE.*`), so esbuild tree-shaking can drop
// the unused half. The result is assembled onto `window.THREE` to preserve the global
// the legacy code expects — NO consumer files change.
//
// Two non-obvious requirements (both documented in the plan memory):
//   1. window.THREE must be a MUTABLE object. ParticleEmitter.js / StarParticleEmitter.js
//      assign onto it (e.g. THREE.AdditiveAlphaBlending = 4). A frozen ES-module
//      namespace would throw on those writes, so we build a plain mutable object here.
//   2. AdditiveAlphaBlending is a CUSTOM FV constant (= 4), not a real THREE export.
//      We seed it so it exists before the particle emitters reassign it (they set the
//      same value); without the seed, code paths that read it before an emitter runs
//      would see undefined.
//
// To add a THREE tool later (e.g. for new FX): add it to the import list below and to
// the assigned object, then rebuild. Tree-shaking keeps it + its deps automatically.

import {
    // Math / core
    Color,
    Vector2,
    Vector3,
    Matrix4,
    Plane,
    Triangle,
    // Scene graph
    Object3D,
    Group,
    Scene,
    Mesh,
    Line,
    LineSegments,
    Sprite,
    Points,
    // Cameras
    OrthographicCamera,
    // Renderer
    WebGLRenderer,
    // Geometry
    BufferGeometry,
    PlaneGeometry,
    ShapeGeometry,
    CircleGeometry,
    InstancedBufferGeometry,
    BufferAttribute,
    Float32BufferAttribute,
    InterleavedBufferAttribute,
    // Shapes / curves
    Shape,
    Path,
    CubicBezierCurve,
    QuadraticBezierCurve,
    // Materials
    Material,
    MeshBasicMaterial,
    LineBasicMaterial,
    ShaderMaterial,
    RawShaderMaterial,
    SpriteMaterial,
    // Textures / loaders
    Texture,
    DataTexture,
    CanvasTexture,
    TextureLoader,
    ImageBitmapLoader,
    // Helpers
    ArrowHelper,
    PlaneHelper,
    // Misc
    AmbientLight,
    Raycaster,
    MathUtils,
    // Constants — blending
    NormalBlending,
    AdditiveBlending,
    SubtractiveBlending,
    MultiplyBlending,
    // Constants — sides / wrapping / filters
    DoubleSide,
    RepeatWrapping,
    LinearFilter,
    NearestFilter,
    LinearMipmapLinearFilter,
    // Constants — colour space / draw usage
    SRGBColorSpace,
    DynamicDrawUsage,
    // Constants — stencil
    AlwaysStencilFunc,
    EqualStencilFunc,
    ReplaceStencilOp,
} from 'three';

const THREE = {
    Color, Vector2, Vector3, Matrix4, Plane, Triangle,
    Object3D, Group, Scene, Mesh, Line, LineSegments, Sprite, Points,
    OrthographicCamera,
    WebGLRenderer,
    BufferGeometry, PlaneGeometry, ShapeGeometry, CircleGeometry, InstancedBufferGeometry,
    BufferAttribute, Float32BufferAttribute, InterleavedBufferAttribute,
    Shape, Path, CubicBezierCurve, QuadraticBezierCurve,
    Material, MeshBasicMaterial, LineBasicMaterial, ShaderMaterial, RawShaderMaterial, SpriteMaterial,
    Texture, DataTexture, CanvasTexture, TextureLoader, ImageBitmapLoader,
    ArrowHelper, PlaneHelper,
    AmbientLight, Raycaster, MathUtils,
    NormalBlending, AdditiveBlending, SubtractiveBlending, MultiplyBlending,
    DoubleSide, RepeatWrapping, LinearFilter, NearestFilter, LinearMipmapLinearFilter,
    SRGBColorSpace, DynamicDrawUsage,
    AlwaysStencilFunc, EqualStencilFunc, ReplaceStencilOp,

    // Custom FV blending constant (not a real THREE export). The particle emitters
    // reassign this to the same value at runtime; seed it so it's never undefined.
    AdditiveAlphaBlending: 4,
};

window.THREE = THREE;

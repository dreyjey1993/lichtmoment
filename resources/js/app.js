import * as THREE from 'three';

const canvas = document.getElementById('hero-canvas');
if (!canvas) throw new Error('Canvas #hero-canvas not found');

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
camera.position.z = 30;

const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

// Golden bokeh particles
const particleCount = 80;
const positions = new Float32Array(particleCount * 3);
const sizes = new Float32Array(particleCount);
const opacities = new Float32Array(particleCount);
const velocities = [];

for (let i = 0; i < particleCount; i++) {
    positions[i * 3] = (Math.random() - 0.5) * 60;
    positions[i * 3 + 1] = (Math.random() - 0.5) * 60;
    positions[i * 3 + 2] = (Math.random() - 0.5) * 30;
    sizes[i] = Math.random() * 3 + 1;
    opacities[i] = Math.random() * 0.5 + 0.1;
    velocities.push({
        x: (Math.random() - 0.5) * 0.01,
        y: (Math.random() - 0.5) * 0.01,
        z: (Math.random() - 0.5) * 0.005,
    });
}

const geometry = new THREE.BufferGeometry();
geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
geometry.setAttribute('size', new THREE.BufferAttribute(sizes, 1));
geometry.setAttribute('opacity', new THREE.BufferAttribute(opacities, 1));

const material = new THREE.ShaderMaterial({
    uniforms: { color: { value: new THREE.Color(0xc9a94e) } },
    vertexShader: `
        attribute float size;
        attribute float opacity;
        varying float vOpacity;
        void main() {
            vOpacity = opacity;
            vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);
            gl_PointSize = size * (200.0 / -mvPosition.z);
            gl_Position = projectionMatrix * mvPosition;
        }
    `,
    fragmentShader: `
        uniform vec3 color;
        varying float vOpacity;
        void main() {
            float dist = length(gl_PointCoord - vec2(0.5));
            if (dist > 0.5) discard;
            float alpha = (1.0 - dist * 2.0) * vOpacity;
            gl_FragColor = vec4(color, alpha);
        }
    `,
    transparent: true,
    depthWrite: false,
});

const particles = new THREE.Points(geometry, material);
scene.add(particles);

function animate() {
    requestAnimationFrame(animate);
    const pos = geometry.attributes.position.array;
    for (let i = 0; i < particleCount; i++) {
        pos[i * 3] += velocities[i].x;
        pos[i * 3 + 1] += velocities[i].y;
        pos[i * 3 + 2] += velocities[i].z;
        if (Math.abs(pos[i * 3]) > 30) velocities[i].x *= -1;
        if (Math.abs(pos[i * 3 + 1]) > 30) velocities[i].y *= -1;
        if (Math.abs(pos[i * 3 + 2]) > 15) velocities[i].z *= -1;
    }
    geometry.attributes.position.needsUpdate = true;
    renderer.render(scene, camera);
}
animate();

window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

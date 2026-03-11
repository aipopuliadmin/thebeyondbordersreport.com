/**
 * Brand Visualization 3D
 * Three.js-based interactive brand portfolio visualization
 * 
 * @package Beond_Custom
 */

(function($) {
    'use strict';

    /**
     * Initialize visualization on document ready
     */
    $(document).ready(function() {
        const vizManager = new BrandVizManager();
        vizManager.init();
    });

    /**
     * Brand Visualization Manager
     * Handles AJAX calls and UI interactions
     */
    class BrandVizManager {
        constructor() {
            this.ajaxurl = window.brandVizConfig?.ajaxurl || '/wp-admin/admin-ajax.php';
            this.brandViz = null;
            this.$rootSelect = $('#root-division');
            this.$refreshBtn = $('#refresh-viz');
            this.$statsBtn = $('#toggle-stats');
            this.$container = $('#visualization-container');
        }

        init() {
            this.setupEventListeners();
            this.updateVisualization();
        }

        setupEventListeners() {
            this.$refreshBtn.on('click', () => this.updateVisualization());
            
            this.$rootSelect.on('change', () => {
                this.$refreshBtn.text('Update View');
            });
            
            this.$statsBtn.on('click', () => {
                if (this.brandViz) {
                    this.brandViz.toggleStats();
                }
            });

            // Keyboard accessibility
            this.$rootSelect.on('keydown', (e) => {
                if (e.key === 'Enter') {
                    this.updateVisualization();
                }
            });
        }

        updateVisualization() {
            const rootId = this.$rootSelect.val();
            
            this.$container.html(`
                <div class="viz-loading" role="status" aria-live="polite">
                    <div class="viz-loading-icon" aria-hidden="true">🔄</div>
                    <p class="viz-loading-text">Loading visualization...</p>
                </div>
            `);
            
            $.get(this.ajaxurl, {
                action: 'get_brand_hierarchy',
                parent_id: rootId
            })
            .done((response) => {
                if (response.success) {
                    this.initVisualization(response.data.data);
                } else {
                    this.showError('Failed to load data. Please try again.');
                }
            })
            .fail(() => {
                this.showError('Network error. Please check your connection and try again.');
            });
        }

        initVisualization(data) {
            // Clean up existing visualization
            if (this.brandViz) {
                this.brandViz.cleanup();
            }
            
            this.$container.empty();
            this.brandViz = new BrandVisualization3D('visualization-container', data);
        }

        showError(message) {
            this.$container.html(`
                <div class="viz-error" role="alert">
                    <div class="viz-loading-icon" aria-hidden="true">⚠️</div>
                    <p class="viz-error-text">${message}</p>
                </div>
            `);
        }
    }

    /**
     * 3D Brand Visualization Class
     * Manages Three.js scene, camera, and interactions
     */
    class BrandVisualization3D {
        constructor(containerId, data) {
            this.container = document.getElementById(containerId);
            this.data = this.convertWordPressData(data);
            
            // Configuration
            this.config = {
                camera: {
                    fov: 60,
                    near: 0.1,
                    far: 5000,
                    position: { x: 0, y: 0, z: 800 }
                },
                node: {
                    radiusLarge: 40,
                    radiusMedium: 25,
                    radiusSmall: 15,
                    segments: 32
                },
                layout: {
                    levelDistance: 250,
                    nodeSpacingFactor: 1.5,
                    orbitRadius: 200,
                    horizontalSpacing: 300,
                    verticalSpacing: 200
                },
                animation: {
                    duration: 800,
                    easing: t => t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1
                },
                interaction: {
                    mouseWheelSpeed: 0.1,
                    hoverScale: 1.2,
                    cameraOffsetFactor: 0.3
                },
                text: {
                    fontSize: 14,
                    fontFamily: 'Arial, sans-serif',
                    color: '#ffffff',
                    backgroundColor: 'rgba(0, 0, 0, 0.7)'
                }
            };
            
            this.init();
        }

        /**
         * Convert WordPress data format to visualization format
         */
        convertWordPressData(wpData) {
            const converted = {
                id: wpData.id || 0,
                name: wpData.name || 'Root',
                color: wpData.color || '#003265',
                level: 0,
                children: []
            };
            
            if (wpData.children && wpData.children.length > 0) {
                converted.children = wpData.children.map(child => this.convertWordPressDataRecursive(child, 1));
            }
            
            if (wpData.brands && wpData.brands.length > 0) {
                converted.children = converted.children.concat(wpData.brands.map(brand => ({
                    id: 'brand_' + brand.id,
                    name: brand.name,
                    color: brand.color || '#999999',
                    level: 1,
                    isBrand: true,
                    website: brand.website,
                    logo: brand.logo_url,
                    children: []
                })));
            }
            
            return converted;
        }

        convertWordPressDataRecursive(node, level) {
            const converted = {
                id: node.id,
                name: node.name,
                color: node.color || '#003265',
                level: level,
                children: []
            };
            
            if (node.children && node.children.length > 0) {
                converted.children = node.children.map(child => this.convertWordPressDataRecursive(child, level + 1));
            }
            
            if (node.brands && node.brands.length > 0) {
                converted.children = converted.children.concat(node.brands.map(brand => ({
                    id: 'brand_' + brand.id,
                    name: brand.name,
                    color: brand.color || '#999999',
                    level: level + 1,
                    isBrand: true,
                    website: brand.website,
                    logo: brand.logo_url,
                    children: []
                })));
            }
            
            return converted;
        }

        /**
         * Initialize Three.js scene
         */
        init() {
            // Scene setup
            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0x0a0a1a);
            
            // Camera setup
            const aspect = this.container.clientWidth / this.container.clientHeight;
            this.camera = new THREE.PerspectiveCamera(
                this.config.camera.fov,
                aspect,
                this.config.camera.near,
                this.config.camera.far
            );
            this.camera.position.set(
                this.config.camera.position.x,
                this.config.camera.position.y,
                this.config.camera.position.z
            );
            
            // Renderer setup
            this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
            this.renderer.setPixelRatio(window.devicePixelRatio);
            this.container.appendChild(this.renderer.domElement);
            
            // Lighting
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            this.scene.add(ambientLight);
            
            const pointLight1 = new THREE.PointLight(0xffffff, 0.8);
            pointLight1.position.set(500, 500, 500);
            this.scene.add(pointLight1);
            
            const pointLight2 = new THREE.PointLight(0xC9A961, 0.5); // Gold accent
            pointLight2.position.set(-500, -500, 300);
            this.scene.add(pointLight2);
            
            // Initialize objects
            this.nodes = new Map();
            this.lines = [];
            this.labels = new Map();
            this.expandedNodes = new Set();
            this.geometryPool = this.createGeometryPool();
            this.materialCache = new Map();
            this.spriteCache = new Map();
            
            // Interaction
            this.raycaster = new THREE.Raycaster();
            this.mouse = new THREE.Vector2();
            this.isDragging = false;
            this.mouseDownPosition = { x: 0, y: 0 };
            this.hoveredNode = null;
            this.dragThreshold = 5;
            this.isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
            
            // Stats
            this.stats = this.createStats();
            this.statsVisible = false;
            
            // Create visualization
            this.createGraph(this.data);
            
            // Event listeners
            this.setupEventListeners();
            
            // Start animation loop
            this.animate();
        }

        createGeometryPool() {
            return {
                large: new THREE.SphereGeometry(this.config.node.radiusLarge, this.config.node.segments, this.config.node.segments),
                medium: new THREE.SphereGeometry(this.config.node.radiusMedium, this.config.node.segments, this.config.node.segments),
                small: new THREE.SphereGeometry(this.config.node.radiusSmall, this.config.node.segments, this.config.node.segments)
            };
        }

        getMaterial(color) {
            if (!this.materialCache.has(color)) {
                this.materialCache.set(color, new THREE.MeshPhongMaterial({
                    color: new THREE.Color(color),
                    emissive: new THREE.Color(color).multiplyScalar(0.2),
                    shininess: 100,
                    specular: 0x444444
                }));
            }
            return this.materialCache.get(color);
        }

        createTextSprite(text, color = this.config.text.color) {
            const cacheKey = text + color;
            if (this.spriteCache.has(cacheKey)) {
                return this.spriteCache.get(cacheKey).clone();
            }
            
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const fontSize = this.config.text.fontSize;
            context.font = `Bold ${fontSize}px ${this.config.text.fontFamily}`;
            
            const textWidth = context.measureText(text).width;
            canvas.width = textWidth + 20;
            canvas.height = fontSize + 10;
            
            context.font = `Bold ${fontSize}px ${this.config.text.fontFamily}`;
            context.fillStyle = this.config.text.backgroundColor;
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.fillStyle = color;
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(text, canvas.width / 2, canvas.height / 2);
            
            const texture = new THREE.CanvasTexture(canvas);
            const spriteMaterial = new THREE.SpriteMaterial({ map: texture });
            const sprite = new THREE.Sprite(spriteMaterial);
            sprite.scale.set(canvas.width * 0.5, canvas.height * 0.5, 1);
            
            this.spriteCache.set(cacheKey, sprite);
            return sprite.clone();
        }

        createGraph(data, parentPosition = null, angle = 0, level = 0, index = 0, totalSiblings = 1) {
            const position = parentPosition ? this.calculateNodePosition(parentPosition, angle, level, index, totalSiblings) : new THREE.Vector3(0, 0, 0);
            const node = this.createNode(data, position, level);
            
            this.nodes.set(data.id, { mesh: node, data: data, position: position, level: level, children: [] });
            this.scene.add(node);
            
            const label = this.createTextSprite(data.name);
            label.position.copy(position);
            label.position.y += level === 0 ? 60 : (level === 1 ? 40 : 30);
            this.labels.set(data.id, label);
            this.scene.add(label);
            
            if (parentPosition) {
                const line = this.createLine(parentPosition, position, data.color);
                this.lines.push(line);
                this.scene.add(line);
            }
        }

        calculateNodePosition(parentPos, angle, level, index, totalSiblings) {
            // Level 1: Horizontal alignment
            if (level === 1) {
                const totalWidth = (totalSiblings - 1) * this.config.layout.horizontalSpacing;
                const startX = parentPos.x - totalWidth / 2;
                return new THREE.Vector3(
                    startX + index * this.config.layout.horizontalSpacing,
                    parentPos.y - this.config.layout.verticalSpacing,
                    parentPos.z
                );
            }
            
            // Level 2+: Circular around parent
            const distance = this.config.layout.orbitRadius;
            return new THREE.Vector3(
                parentPos.x + Math.cos(angle) * distance,
                parentPos.y + Math.sin(angle) * distance,
                parentPos.z
            );
        }

        createNode(data, position, level) {
            const geometry = level === 0 ? this.geometryPool.large : (level === 1 ? this.geometryPool.medium : this.geometryPool.small);
            const material = this.getMaterial(data.color);
            const mesh = new THREE.Mesh(geometry, material);
            mesh.position.copy(position);
            mesh.userData = { id: data.id, data: data };
            
            // Add tabindex for keyboard accessibility
            mesh.userData.tabIndex = 0;
            
            return mesh;
        }

        createLine(start, end, color) {
            const material = new THREE.LineBasicMaterial({ 
                color: new THREE.Color(color), 
                transparent: true, 
                opacity: 0.3 
            });
            const geometry = new THREE.BufferGeometry().setFromPoints([start, end]);
            return new THREE.Line(geometry, material);
        }

        toggleNode(nodeId) {
            const nodeData = this.nodes.get(nodeId);
            if (!nodeData || !nodeData.data.children || nodeData.data.children.length === 0) return;
            
            if (this.expandedNodes.has(nodeId)) {
                this.collapseNode(nodeId);
            } else {
                this.expandNode(nodeId);
            }
        }

        expandNode(nodeId) {
            const nodeData = this.nodes.get(nodeId);
            if (!nodeData) return;
            
            this.expandedNodes.add(nodeId);
            const children = nodeData.data.children;
            
            children.forEach((child, i) => {
                const angle = (Math.PI * 2 * i) / children.length;
                this.createGraph(child, nodeData.position, angle, nodeData.level + 1, i, children.length);
            });
            
            // Announce to screen readers
            this.announceToScreenReader(`Expanded ${nodeData.data.name}, showing ${children.length} items`);
            
            // Animate camera to focus on the expanded node (only for level 1+)
            if (nodeData.level >= 1) {
                this.animateCameraToNode(nodeData.position, children.length);
            }
        }

        animateCameraToNode(targetPosition, childCount) {
            const startPos = this.camera.position.clone();
            const endPos = new THREE.Vector3(
                targetPosition.x,
                targetPosition.y,
                targetPosition.z + 600
            );
            
            const duration = 800;
            const startTime = Date.now();
            
            const animateCamera = () => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = this.config.animation.easing(progress);
                
                this.camera.position.lerpVectors(startPos, endPos, eased);
                this.camera.lookAt(targetPosition);
                
                if (progress < 1) {
                    requestAnimationFrame(animateCamera);
                }
            };
            
            animateCamera();
        }

        collapseNode(nodeId) {
            const nodeData = this.nodes.get(nodeId);
            if (!nodeData) return;
            
            this.expandedNodes.delete(nodeId);
            this.removeChildNodes(nodeData.data.children);
            
            // Announce to screen readers
            this.announceToScreenReader(`Collapsed ${nodeData.data.name}`);
        }

        removeChildNodes(children) {
            children.forEach(child => {
                const childNode = this.nodes.get(child.id);
                if (childNode) {
                    this.scene.remove(childNode.mesh);
                    this.nodes.delete(child.id);
                    
                    const label = this.labels.get(child.id);
                    if (label) {
                        this.scene.remove(label);
                        this.labels.delete(child.id);
                    }
                    
                    if (child.children && child.children.length > 0) {
                        this.removeChildNodes(child.children);
                    }
                }
            });
            
            this.lines.forEach(line => this.scene.remove(line));
            this.lines = [];
            
            this.nodes.forEach((nodeData, id) => {
                const parentNode = this.findParentNode(id);
                if (parentNode) {
                    const line = this.createLine(parentNode.position, nodeData.position, nodeData.data.color);
                    this.lines.push(line);
                    this.scene.add(line);
                }
            });
        }

        findParentNode(nodeId) {
            for (const [id, nodeData] of this.nodes) {
                if (nodeData.data.children && nodeData.data.children.some(c => c.id === nodeId)) {
                    return nodeData;
                }
            }
            return null;
        }

        setupEventListeners() {
            const canvas = this.renderer.domElement;
            
            // Mouse/Touch events
            if (this.isTouchDevice) {
                canvas.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: false });
                canvas.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: false });
                canvas.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: false });
            } else {
                canvas.addEventListener('mousedown', (e) => this.handleMouseDown(e));
                canvas.addEventListener('mousemove', (e) => this.handleMouseMove(e));
                canvas.addEventListener('mouseup', (e) => this.handleMouseUp(e));
                canvas.addEventListener('click', (e) => this.handleClick(e));
            }
            
            canvas.addEventListener('wheel', (e) => this.handleWheel(e), { passive: false });
            
            window.addEventListener('resize', () => this.handleResize());
            
            // Keyboard navigation
            canvas.setAttribute('tabindex', '0');
            canvas.addEventListener('keydown', (e) => this.handleKeyDown(e));
        }

        handleMouseDown(e) {
            this.mouseDownPosition = { x: e.clientX, y: e.clientY };
        }

        handleMouseMove(e) {
            this.mouse.x = (e.offsetX / this.container.clientWidth) * 2 - 1;
            this.mouse.y = -(e.offsetY / this.container.clientHeight) * 2 + 1;
            
            // Subtle camera offset based on mouse position
            const centerX = this.container.clientWidth / 2;
            const centerY = this.container.clientHeight / 2;
            const offsetX = (e.offsetX - centerX) * this.config.interaction.cameraOffsetFactor;
            const offsetY = (centerY - e.offsetY) * this.config.interaction.cameraOffsetFactor;
            
            this.camera.position.x = offsetX;
            this.camera.position.y = offsetY;
            
            if (this.mouseDownPosition) {
                const dragDistance = Math.sqrt(
                    Math.pow(e.clientX - this.mouseDownPosition.x, 2) + 
                    Math.pow(e.clientY - this.mouseDownPosition.y, 2)
                );
                
                if (dragDistance > this.dragThreshold) {
                    this.isDragging = true;
                }
            }
            
            this.checkHover();
        }

        handleMouseUp() {
            this.mouseDownPosition = null;
            this.isDragging = false;
        }

        handleTouchStart(e) {
            if (e.touches.length === 1) {
                this.mouseDownPosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            }
        }

        handleTouchMove(e) {
            e.preventDefault();
            if (e.touches.length === 1) {
                const touch = e.touches[0];
                const rect = this.renderer.domElement.getBoundingClientRect();
                this.mouse.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
                this.mouse.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;
                
                this.checkHover();
            }
        }

        handleTouchEnd(e) {
            if (!this.isDragging && this.mouseDownPosition) {
                this.handleClick(e);
            }
            this.mouseDownPosition = null;
            this.isDragging = false;
        }

        handleWheel(e) {
            e.preventDefault();
            const delta = e.deltaY * this.config.interaction.mouseWheelSpeed;
            this.camera.position.z = Math.max(200, Math.min(1500, this.camera.position.z + delta));
        }

        handleResize() {
            const width = this.container.clientWidth;
            const height = this.container.clientHeight;
            this.camera.aspect = width / height;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(width, height);
        }

        handleKeyDown(e) {
            if (this.hoveredNode) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const nodeId = this.hoveredNode.userData.id;
                    const nodeData = this.hoveredNode.userData.data;
                    
                    if (nodeData.isBrand && nodeData.website) {
                        window.open(nodeData.website, '_blank');
                        this.announceToScreenReader(`Opening ${nodeData.name} website`);
                    } else {
                        this.toggleNode(nodeId);
                    }
                }
            }
            
            // Zoom with + and - keys
            if (e.key === '+' || e.key === '=') {
                this.camera.position.z = Math.max(200, this.camera.position.z - 50);
            } else if (e.key === '-' || e.key === '_') {
                this.camera.position.z = Math.min(1500, this.camera.position.z + 50);
            }
        }

        checkHover() {
            this.raycaster.setFromCamera(this.mouse, this.camera);
            const meshes = Array.from(this.nodes.values()).map(n => n.mesh);
            const intersects = this.raycaster.intersectObjects(meshes);
            
            if (intersects.length > 0) {
                const newHovered = intersects[0].object;
                if (this.hoveredNode !== newHovered) {
                    if (this.hoveredNode) {
                        this.hoveredNode.scale.set(1, 1, 1);
                    }
                    this.hoveredNode = newHovered;
                    this.hoveredNode.scale.set(
                        this.config.interaction.hoverScale, 
                        this.config.interaction.hoverScale, 
                        this.config.interaction.hoverScale
                    );
                    this.renderer.domElement.style.cursor = 'pointer';
                    
                    // Announce to screen readers
                    const nodeName = this.hoveredNode.userData.data.name;
                    const nodeType = this.hoveredNode.userData.data.isBrand ? 'brand' : 'division';
                    this.announceToScreenReader(`${nodeName}, ${nodeType}`);
                }
            } else {
                if (this.hoveredNode) {
                    this.hoveredNode.scale.set(1, 1, 1);
                    this.hoveredNode = null;
                }
                this.renderer.domElement.style.cursor = 'default';
            }
        }

        handleClick(e) {
            if (this.isDragging) return;
            
            // Update mouse position for click
            const rect = this.renderer.domElement.getBoundingClientRect();
            const clientX = e.clientX || (e.changedTouches && e.changedTouches[0].clientX);
            const clientY = e.clientY || (e.changedTouches && e.changedTouches[0].clientY);
            
            this.mouse.x = ((clientX - rect.left) / rect.width) * 2 - 1;
            this.mouse.y = -((clientY - rect.top) / rect.height) * 2 + 1;
            
            this.raycaster.setFromCamera(this.mouse, this.camera);
            const meshes = Array.from(this.nodes.values()).map(n => n.mesh);
            const intersects = this.raycaster.intersectObjects(meshes);
            
            if (intersects.length > 0) {
                const nodeId = intersects[0].object.userData.id;
                const nodeData = intersects[0].object.userData.data;
                
                if (nodeData.isBrand && nodeData.website) {
                    window.open(nodeData.website, '_blank');
                    this.announceToScreenReader(`Opening ${nodeData.name} website`);
                } else {
                    this.toggleNode(nodeId);
                }
            }
        }

        createStats() {
            const stats = document.createElement('div');
            stats.className = 'viz-stats';
            stats.setAttribute('role', 'status');
            stats.setAttribute('aria-live', 'polite');
            this.container.appendChild(stats);
            return stats;
        }

        updateStats() {
            if (!this.statsVisible) return;
            
            const nodeCount = this.nodes.size;
            const triangleCount = this.scene.children.reduce((sum, obj) => {
                if (obj.geometry) {
                    const positions = obj.geometry.attributes.position;
                    return sum + (positions ? positions.count / 3 : 0);
                }
                return sum;
            }, 0);
            
            const fps = Math.round(1000 / (performance.now() - (this.lastFrameTime || performance.now())));
            const memory = (performance.memory?.usedJSHeapSize / 1048576 || 0).toFixed(2);
            
            this.stats.innerHTML = `
                <div class="viz-stats-item viz-stats-fps">FPS: ${fps}</div>
                <div class="viz-stats-item viz-stats-nodes">Nodes: ${nodeCount}</div>
                <div class="viz-stats-item viz-stats-triangles">Triangles: ${Math.round(triangleCount)}</div>
                <div class="viz-stats-item viz-stats-memory">Memory: ${memory} MB</div>
            `;
            this.lastFrameTime = performance.now();
        }

        toggleStats() {
            this.statsVisible = !this.statsVisible;
            this.stats.classList.toggle('active', this.statsVisible);
            this.announceToScreenReader(`Stats ${this.statsVisible ? 'shown' : 'hidden'}`);
        }

        announceToScreenReader(message) {
            // Create or update ARIA live region
            let announcer = document.getElementById('viz-announcer');
            if (!announcer) {
                announcer = document.createElement('div');
                announcer.id = 'viz-announcer';
                announcer.className = 'sr-only';
                announcer.setAttribute('role', 'status');
                announcer.setAttribute('aria-live', 'polite');
                announcer.setAttribute('aria-atomic', 'true');
                document.body.appendChild(announcer);
            }
            
            announcer.textContent = message;
        }

        animate() {
            requestAnimationFrame(() => this.animate());
            this.updateStats();
            this.renderer.render(this.scene, this.camera);
        }

        cleanup() {
            if (this.renderer) {
                this.renderer.dispose();
                if (this.renderer.domElement && this.renderer.domElement.parentNode === this.container) {
                    this.container.removeChild(this.renderer.domElement);
                }
            }
            if (this.stats && this.stats.parentNode === this.container) {
                this.container.removeChild(this.stats);
            }
            if (this.nodes) this.nodes.clear();
            if (this.labels) this.labels.clear();
            if (this.materialCache) this.materialCache.clear();
            if (this.spriteCache) this.spriteCache.clear();
            
            // Remove announcer
            const announcer = document.getElementById('viz-announcer');
            if (announcer) {
                announcer.remove();
            }
        }
    }

    // Make globally available
    window.BrandVisualization3D = BrandVisualization3D;

})(jQuery);

// Menu Mobile Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Menu Hamburguer
    const mobileMenu = document.querySelector('.mobile-menu');
    const navMenu = document.querySelector('nav ul');
    const navLinks = document.querySelectorAll('nav ul li a');

    // Abrir/Fechar menu hamburguer
    if (mobileMenu && navMenu) {
        mobileMenu.addEventListener('click', function (e) {
            e.stopPropagation();
            navMenu.classList.toggle('show');
            mobileMenu.classList.toggle('active');
        });

        // Fechar menu ao clicar em um link
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                navMenu.classList.remove('show');
                mobileMenu.classList.remove('active');
            });
        });

        // Fechar menu ao clicar fora
        document.addEventListener('click', function (event) {
            if (!navMenu.contains(event.target) && !mobileMenu.contains(event.target)) {
                navMenu.classList.remove('show');
                mobileMenu.classList.remove('active');
            }
        });

        // Fechar menu ao redimensionar para desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                navMenu.classList.remove('show');
                mobileMenu.classList.remove('active');
            }
        });
    }

    // Carrossel Functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    const totalSlides = slides.length;
    let slideInterval;

    // Função para mostrar slide específico
    function showSlide(n) {
        // Remove classe active de todos os slides e indicadores
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        indicators.forEach(indicator => indicator.classList.remove('active'));

        // Atualiza slide atual
        currentSlide = n;

        // Garante que currentSlide esteja dentro dos limites
        if (currentSlide >= totalSlides) currentSlide = 0;
        if (currentSlide < 0) currentSlide = totalSlides - 1;

        // Adiciona classe active ao slide atual
        slides[currentSlide].classList.add('active');
        indicators[currentSlide].classList.add('active');
    }

    // Função para próximo slide
    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    // Função para slide anterior
    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    // Função para ir para slide específico
    function goToSlide(n) {
        showSlide(n);
    }

    // Inicializa o carrossel se existir
    function initCarousel() {
        if (slides.length > 0) {
            showSlide(0);

            // Auto-play
            slideInterval = setInterval(nextSlide, 5000);

            // Event listeners para controles
            const prevButton = document.querySelector('.carousel-control.prev');
            const nextButton = document.querySelector('.carousel-control.next');

            if (prevButton) {
                prevButton.addEventListener('click', prevSlide);
            }

            if (nextButton) {
                nextButton.addEventListener('click', nextSlide);
            }

            // Event listeners para indicadores
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => goToSlide(index));
            });

            // Pausa o auto-play quando o mouse está sobre o carrossel
            const carousel = document.querySelector('.hero-carousel');
            if (carousel) {
                carousel.addEventListener('mouseenter', () => {
                    clearInterval(slideInterval);
                });

                carousel.addEventListener('mouseleave', () => {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 5000);
                });
            }

            // Navegação por teclado
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') prevSlide();
                if (e.key === 'ArrowRight') nextSlide();
            });
        }
    }

    // Inicializa o carrossel
    initCarousel();
});

// Validação e envio do formulário
(function () {
    'use strict';

    // Selecionar o formulário
    const form = document.getElementById('contactForm');

    // Se o formulário não existir, para a execução
    if (!form) return;

    const submitButton = document.getElementById('submitButton');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const honeypot = document.getElementById('honeypot');

    // Esconder mensagens ao carregar a página
    if (successMessage) successMessage.classList.add('d-none');
    if (errorMessage) errorMessage.classList.add('d-none');

    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            if (value.length > 0) {
                if (value.length <= 2) {
                    value = `(${value}`;
                } else if (value.length <= 7) {
                    value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
                } else if (value.length <= 11) {
                    value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
                }
            }

            e.target.value = value;
        });
    }

    // Adicionar evento de submit
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        // Check validity
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // Verificar honeypot
        if (honeypot && honeypot.value !== '') {
            return;
        }

        // Coletar dados
        const formData = new FormData(form);
        const nome = formData.get('nome');
        const telefone = formData.get('telefone');
        const email = formData.get('email') || 'Não informado';
        const endereco = formData.get('endereco') || 'Não informado';
        const mensagem = formData.get('mensagem') || '';

        // Coletar serviços selecionados
        const servicos = [];
        form.querySelectorAll('input[name="servicos[]"]:checked').forEach(checkbox => {
            servicos.push(checkbox.value);
        });
        const servicosTexto = servicos.length > 0 ? servicos.join(', ') : 'Nenhum específico';

        // Montar mensagem para o WhatsApp
        const text = `*Nova Solicitação de Orçamento*\n\n` +
            `*Nome:* ${nome}\n` +
            `*Telefone:* ${telefone}\n` +
            `*Email:* ${email}\n` +
            `*Endereço:* ${endereco}\n\n` +
            `*Serviços de Interesse:*\n${servicosTexto}\n\n` +
            `*Mensagem:*\n${mensagem}`;

        // Codificar para URL
        const encodedText = encodeURIComponent(text);
        const whatsappUrl = `https://wa.me/558798000202?text=${encodedText}`;

        // Abrir WhatsApp
        window.open(whatsappUrl, '_blank');

        // Feedback visual simples (opcional, já que abre nova aba)
        // if (successMessage) {
        //     successMessage.classList.remove('d-none');
        //     successMessage.innerHTML = '<strong>Redirecionando para o WhatsApp...</strong>';
        // }
    }, false);

    // Validação em tempo real para melhor UX
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('blur', () => {
            if (input.checkValidity()) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        });
    });
})();

// Smooth Scroll para links âncora
document.addEventListener('DOMContentLoaded', function () {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerHeight = document.querySelector('header') ? document.querySelector('header').offsetHeight : 0;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
});

// Scroll Animations
document.addEventListener('DOMContentLoaded', function () {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // Only animate once
            }
        });
    }, observerOptions);

    // Elements to animate
    const animatedElements = document.querySelectorAll('section, .card, .benefit-item, .contact-info, .map-container');

    animatedElements.forEach(el => {
        el.classList.add('fade-in-section');
        observer.observe(el);
    });
});
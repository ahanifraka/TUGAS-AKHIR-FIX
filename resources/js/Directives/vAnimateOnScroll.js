const vAnimateOnScroll = {
    mounted: (el) => {
        el.classList.add('opacity-0', 'translate-y-10', 'transition-all', 'duration-1000', 'ease-out');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        el.classList.remove('opacity-0', 'translate-y-10');
                        el.classList.add('opacity-100', 'translate-y-0');
                    }, 150);
                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        observer.observe(el);
        el._observer = observer;
    },
    unmounted: (el) => {
        if (el._observer) {
            el._observer.disconnect();
        }
    }
};

export default vAnimateOnScroll;

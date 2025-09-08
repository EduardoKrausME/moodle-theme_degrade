document.addEventListener('DOMContentLoaded', function () {

    if (!location.hash) {
        location.hash = '#home';
    }
    document.querySelectorAll('.quickstart-collapse a').forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href').substring(1);
            const target = document.getElementById(targetId);
            // Aguarda 300ms antes de rolar até o elemento.
            setTimeout(() => {
                console.log(target);
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 300);
        });
    });
});

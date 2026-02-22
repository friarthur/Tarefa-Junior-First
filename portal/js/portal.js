    (function() {
       
        lucide.createIcons();

     
        const dropdownBtn = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

      
        const menuItems = document.querySelectorAll('.menu-item');
        const storeSpan = dropdownBtn.querySelector('span');

        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const storeName = this.getAttribute('data-store');
                if (storeName) storeSpan.innerText = storeName;
                dropdownMenu.classList.remove('show');
            });
        });

   
        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                navItems.forEach(n => n.classList.remove('active'));
                this.classList.add('active');
            });
        });
        const fab = document.getElementById('quickActionBtn');
        fab.addEventListener('click', () => {
            alert('Ação rápida: recarregar dados (simulação)');
        });

    })();
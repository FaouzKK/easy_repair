import { CreateDom } from './class/CreateDom.js'


const init = async () => {

    //Configuration du modal
    const requestModal = document.getElementById('new-request-modal');
    const openRequest = document.getElementById('open-request');

    openRequest.addEventListener('click', () => {
        requestModal.classList.remove('hidden-item');
    })

    window.addEventListener('click', (e) => {
        const modal_div = requestModal.children[0];
        if (e.target !== modal_div && e.target != openRequest && !requestModal.classList.contains('hidden-item') && !modal_div.contains(e.target)) {
            requestModal.classList.add('hidden-item');
        }
    });


    //Configuration du sidebar
    const sideOptionLi = document.querySelectorAll('#side-option li')

    sideOptionLi.forEach((li) => {
        li.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();

            if (li.classList.contains('bg-primary')) return;

            window.location.href = li.getAttribute('page');
        })
    })

    //Configuration du logout
    const logoutLi = document.getElementById('logout');

    logoutLi.addEventListener('click', () => {
        window.location.href = window.location.origin + '/logout';
    })

    //Configuration de la pagination
    const pageIntLi = document.querySelectorAll('#main-page-nav  li > a');

    if (pageIntLi.length > 0) {

        pageIntLi.forEach((li) => {
            li.addEventListener('click', e => {
                debugger
                e.preventDefault();
                e.stopPropagation();

                // thisLi = e.target;

                if (e.currentTarget.classList.contains('disabled')) return;
                const url = new URL(window.location.href);
                url.searchParams.set('page', e.currentTarget.getAttribute('page'));
                window.location.href = url.toString()
            })
        })
    }

    //Faire disparaitre l'information au bout de 5sec
    const infoDiv = document.getElementById('request-info');
    if (infoDiv) {
        setTimeout(() => {
            infoDiv.remove();
        }, 5000)
    }

    //Configuration du formulaire de recherche
    const searchButton = document.getElementById('search-button');
    const searchInput = document.getElementById('search-input');

    const searchEvent = async (e) => {
        e.stopPropagation();
        e.preventDefault();

        const searchContent = searchInput.value.trim();
        if (searchContent.length > 0) {
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchContent);

            if (url.searchParams.has('page')) {
                url.searchParams.delete('page');
            }

            window.location.href = url.toString();
        }
    }


    searchButton.addEventListener('click', searchEvent);

    searchInput.addEventListener('keydown', e => {
        debugger
        if (e.key === 'Enter') {
            searchEvent(e);
        }
    })


    //Configuration du modal d'information
    let trList = document.querySelectorAll('#main-table > table tr');

    trList = Array.from(trList).filter(tr => tr.id != 'main-table-header');

    trList.forEach((tr) => {
        tr.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();

            const requestId = tr.getAttribute('requestid');

            //console.log(requestId);
            const backmodal = CreateDom.creatDomContent('div', {
                id: 'request-info-modal'
            })

            const modal_center = CreateDom.creatDomContent('div', {
                id: 'request-modal',
                class: 'p-4'
            })

            backmodal.append(modal_center);

            document.body.append(backmodal);

        })
    })


}



window.addEventListener('DOMContentLoaded', init);
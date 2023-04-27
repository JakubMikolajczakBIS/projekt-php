function datamenu(dzien){
    menuDzien=document.getElementsByClassName('datamenu-dzien');
    menuDzien=Array.from(menuDzien);

    terminy = document.getElementsByClassName('terminy');
    terminy=Array.from(terminy);

    menuDzien.forEach(element => {
        element.dataset.selected="false";
    });

    menuDzien[dzien].dataset.selected="true";

    terminy.forEach(element => {
        element.dataset.selected="false";
    });

    terminy[dzien].dataset.selected="true";
}
"use strict";
document.addEventListener("DOMContentLoaded", initialiser);

function initialiser(evt) {
    var menu = document.querySelector(".burger");
    menu.addEventListener("click", apparaitreMenu);
    var croix = document.querySelector(".fermerMenu");
    croix.addEventListener("click", fermerMenu);
}

function apparaitreMenu(evt) {
    var menuLateral = document.querySelector(".menuCache");
    menuLateral.style.left = "0";
    menuLateral.style.transitionDuration = "0.3s";
    fermerMenu.style.left = "1vw";
    menu.removeEventListener("click", apparaitreMenu);
}

function fermerMenu(evt) {
    var menu = document.querySelector(".burger");
    var menuLateral = document.querySelector(".menuCache");
    menu.addEventListener("click", apparaitreMenu);
    menuLateral.style.left = "-100vw";

}

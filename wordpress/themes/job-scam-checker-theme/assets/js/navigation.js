(function () {
  'use strict';

  var toggle = document.querySelector('[data-menu-toggle]');
  var navigation = document.querySelector('[data-primary-nav]');

  if (!toggle || !navigation) {
    return;
  }

  toggle.addEventListener('click', function () {
    var isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isOpen));
    navigation.classList.toggle('is-open', !isOpen);
  });

  navigation.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      navigation.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.focus();
    }
  });
}());

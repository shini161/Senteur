// Product-page interactions stay page-local without living inside the PHP view.
document.addEventListener('DOMContentLoaded', function () {
  const variantButtons = document.querySelectorAll('.variant-option');
  const variantIdInput = document.getElementById('selected-variant-id');
  const priceEl = document.getElementById('selected-variant-price');
  const stockEl = document.getElementById('selected-variant-stock');
  const mainImage = document.getElementById('product-main-image');
  const thumbnailsWrap = document.getElementById('product-thumbnails');
  const quantityInput = document.querySelector('.product-purchase-form input[name="quantity"]');
  const qtyStepper = document.querySelector('.qty-stepper');

  let clampQuantity = null;

  const renderThumbnails = (images) => {
    if (!thumbnailsWrap) {
      return;
    }

    thumbnailsWrap.innerHTML = '';

    if (!images.length) {
      return;
    }

    images.forEach((imageUrl, index) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'product-thumbnail' + (index === 0 ? ' is-active' : '');
      button.dataset.imageUrl = imageUrl;
      button.innerHTML = '<img src="' + imageUrl + '" alt="Product image">';

      button.addEventListener('click', function () {
        if (mainImage) {
          mainImage.src = imageUrl;
        }

        thumbnailsWrap.querySelectorAll('.product-thumbnail').forEach((thumb) => {
          thumb.classList.remove('is-active');
        });

        button.classList.add('is-active');
      });

      thumbnailsWrap.appendChild(button);
    });
  };

  if (qtyStepper && quantityInput) {
    const decreaseBtn = qtyStepper.querySelector('[data-qty-action="decrease"]');
    const increaseBtn = qtyStepper.querySelector('[data-qty-action="increase"]');

    clampQuantity = () => {
      const min = Number(quantityInput.min || 1);
      const max = Number(quantityInput.max || 1);
      let value = Number(quantityInput.value || min);

      if (Number.isNaN(value)) {
        value = min;
      }

      value = Math.max(min, Math.min(max, value));
      quantityInput.value = String(value);

      const disabled = quantityInput.disabled;
      decreaseBtn.disabled = disabled || value <= min;
      increaseBtn.disabled = disabled || value >= max;
    };

    decreaseBtn.addEventListener('click', () => {
      if (quantityInput.disabled) {
        return;
      }

      quantityInput.stepDown();
      clampQuantity();
    });

    increaseBtn.addEventListener('click', () => {
      if (quantityInput.disabled) {
        return;
      }

      quantityInput.stepUp();
      clampQuantity();
    });

    quantityInput.addEventListener('input', clampQuantity);
    quantityInput.addEventListener('change', clampQuantity);

    clampQuantity();
  }

  variantButtons.forEach((button) => {
    button.addEventListener('click', function () {
      variantButtons.forEach((item) => item.classList.remove('is-selected'));
      button.classList.add('is-selected');

      if (variantIdInput) {
        variantIdInput.value = button.dataset.variantId;
      }

      if (priceEl) {
        priceEl.textContent = '€' + Number(button.dataset.price).toFixed(2);
      }

      const stock = Number(button.dataset.stock);

      if (stockEl) {
        stockEl.textContent = stock > 0 ? 'In stock · ' + stock + ' available' : 'Out of stock';
      }

      if (quantityInput) {
        const maxQuantity = Math.min(stock, 5);
        quantityInput.max = String(maxQuantity > 0 ? maxQuantity : 1);

        if (Number(quantityInput.value) > maxQuantity && maxQuantity > 0) {
          quantityInput.value = String(maxQuantity);
        }

        if (Number(quantityInput.value) < 1) {
          quantityInput.value = '1';
        }

        quantityInput.disabled = stock <= 0;

        if (typeof clampQuantity === 'function') {
          clampQuantity();
        }
      }

      const images = JSON.parse(button.dataset.images || '[]');

      if (images.length > 0) {
        if (mainImage) {
          mainImage.src = images[0];
        }

        renderThumbnails(images);
      } else if (button.dataset.image && mainImage) {
        mainImage.src = '/' + button.dataset.image.replace(/^\/+/, '');
      }
    });
  });

  if (thumbnailsWrap) {
    thumbnailsWrap.querySelectorAll('.product-thumbnail').forEach((thumb) => {
      thumb.addEventListener('click', function () {
        const imageUrl = thumb.dataset.imageUrl;

        if (mainImage) {
          mainImage.src = imageUrl;
        }

        thumbnailsWrap.querySelectorAll('.product-thumbnail').forEach((item) => {
          item.classList.remove('is-active');
        });

        thumb.classList.add('is-active');
      });
    });
  }

  const ratingWidget = document.querySelector('[data-rating-widget]');

  if (ratingWidget) {
    // The rating widget keeps plain radio inputs accessible while the
    // star UI provides faster visual feedback.
    const stars = Array.from(ratingWidget.querySelectorAll('.star-rating-star'));
    const inputs = Array.from(ratingWidget.querySelectorAll('input[name="rating"]'));
    const caption = ratingWidget.querySelector('[data-rating-caption]');

    const labels = {
      1: 'Poor',
      2: 'Fair',
      3: 'Good',
      4: 'Very good',
      5: 'Excellent'
    };

    const getCheckedValue = () => {
      const checked = inputs.find((input) => input.checked);
      return checked ? Number(checked.value) : 0;
    };

    const paintStars = (value) => {
      stars.forEach((star) => {
        const starValue = Number(star.dataset.ratingValue);
        star.classList.toggle('is-active', starValue <= value);
      });

      if (caption) {
        caption.textContent = value > 0 ? labels[value] : 'Select your rating';
      }
    };

    paintStars(getCheckedValue());

    stars.forEach((star) => {
      const value = Number(star.dataset.ratingValue);

      star.addEventListener('mouseenter', () => paintStars(value));
      star.addEventListener('click', () => paintStars(value));
    });

    ratingWidget.querySelector('.star-rating')?.addEventListener('mouseleave', () => {
      paintStars(getCheckedValue());
    });

    inputs.forEach((input) => {
      input.addEventListener('change', () => {
        paintStars(Number(input.value));
      });
    });
  }

  const editBtn = document.querySelector('.review-edit-toggle');
  const reviewForm = document.getElementById('review-form');

  if (editBtn && reviewForm) {
    editBtn.addEventListener('click', () => {
      reviewForm.classList.toggle('is-hidden');
      reviewForm.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
    });
  }
});

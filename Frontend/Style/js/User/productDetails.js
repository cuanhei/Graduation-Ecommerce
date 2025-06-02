
function increase(inputId) {
    const input = document.getElementById(inputId);
    const min = parseInt(input.min);
    const max = parseInt(input.max);
    let value = parseInt(input.value);

    // If input is empty, set it to 0 first
    if (isNaN(value)) {
      value = 0;
    }

    if (value < max) {
      input.value = value + 1;
    } else {
      input.value = max; // Prevent going above max
    }
  }

  function decrease(inputId) {
    const input = document.getElementById(inputId);
    const min = parseInt(input.min);
    let value = parseInt(input.value);

    // If input is empty, set it to 0 first
    if (isNaN(value)) {
      value = 0;
    }

    if (value > min) {
      input.value = value - 1;
    } else {
      input.value = min; // Prevent going below min (0)
    }
  }

document.addEventListener("DOMContentLoaded", function () {
    // Captura os elementos do DOM
    const checkinInput = document.getElementById("checkin");
    const checkoutInput = document.getElementById("checkout");
    const guestsSelect = document.getElementById("guests");
    const simulateButton = document.getElementById("simulate-btn");
    const nightsDisplay = document.getElementById("nights");
    const totalPriceDisplay = document.getElementById("total-price");

    // Função para calcular o total de noites
    function calculateReservation(pricePerNight) {
        const checkinDate = new Date(checkinInput.value);
        const checkoutDate = new Date(checkoutInput.value);

        // Verifica se as datas são válidas
        if (checkinDate && checkoutDate && checkoutDate > checkinDate) {
            const timeDifference = checkoutDate - checkinDate;
            const nights = timeDifference / (1000 * 3600 * 24); // Converte de milissegundos para dias
            const guests = parseInt(guestsSelect.value);

            // Atualiza o número de noites e o preço total
            nightsDisplay.textContent = nights;
            totalPriceDisplay.textContent = (pricePerNight * nights * guests).toFixed(2);
        } else {
            alert("Por favor, insira datas válidas para o Check-in e Check-out.");
        }
    }

    // Adiciona o evento de clique ao botão de simulação
    simulateButton.addEventListener("click", function () {
        const selectedPrice = parseFloat(simulateButton.getAttribute("data-price"));
        calculateReservation(selectedPrice);
    });

    // Adiciona evento de clique nas acomodações para usar o preço da acomodação
    const accommodationButtons = document.querySelectorAll('.simulate-reservation');
    accommodationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedPrice = parseFloat(this.getAttribute("data-price"));
            simulateButton.setAttribute('data-price', selectedPrice);
            calculateReservation(selectedPrice);
        });
    });
});



const pricePerNight = 590;

// Etapas do processo
const step1 = document.getElementById("step-1");
const step2 = document.getElementById("step-2");
const step3 = document.getElementById("step-3");

// Elementos de entrada e resumo
const checkinInput = document.getElementById("checkin");
const checkoutInput = document.getElementById("checkout");
const nightsDisplay = document.getElementById("nights");
const totalPriceDisplay = document.getElementById("total-price");

const summaryDates = document.getElementById("summary-dates");
const summaryNights = document.getElementById("summary-nights");
const summaryTotal = document.getElementById("summary-total");

// Botões
const simulateBtn = document.getElementById("simulate-btn");
const proceedToPaymentBtn = document.getElementById("proceed-to-payment");
const paymentForm = document.getElementById("payment-form");

// Passo 1: Calcular noites e total
simulateBtn.addEventListener("click", () => {
    const checkinDate = new Date(checkinInput.value);
    const checkoutDate = new Date(checkoutInput.value);

    if (checkinDate >= checkoutDate || isNaN(checkinDate) || isNaN(checkoutDate)) {
        alert("Por favor, insira datas válidas.");
        return;
    }

    const timeDifference = checkoutDate - checkinDate;
    const nights = timeDifference / (1000 * 60 * 60 * 24);

    nightsDisplay.textContent = nights;
    totalPriceDisplay.textContent = nights * pricePerNight;

    summaryDates.textContent = `${checkinInput.value} - ${checkoutInput.value}`;
    summaryNights.textContent = nights;
    summaryTotal.textContent = nights * pricePerNight;

    step1.style.display = "none";
    step2.style.display = "block";
});

// Passo 2: Avançar para o pagamento
proceedToPaymentBtn.addEventListener("click", () => {
    step2.style.display = "none";
    step3.style.display = "block";
});

// Passo 3: Finalizar reserva
paymentForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    alert(`Reserva finalizada com sucesso! Forma de pagamento: ${paymentMethod.toUpperCase()}`);
    console.log(`Forma de pagamento escolhida: ${paymentMethod}`);
});

// Função para calcular o total da reserva
function calcularReserva(precoPorNoite, dataEntrada, dataSaida) {
    const dataEntradaObj = new Date(dataEntrada);
    const dataSaidaObj = new Date(dataSaida);
    
    // Verificar se as datas são válidas
    if (isNaN(dataEntradaObj) || isNaN(dataSaidaObj)) {
        alert("Por favor, insira datas válidas.");
        return;
    }

    const diffTime = dataSaidaObj - dataEntradaObj; // Diferença de tempo em milissegundos
    const diffDays = diffTime / (1000 * 3600 * 24); // Converter para dias

    if (diffDays <= 0) {
        alert("A data de saída deve ser maior que a data de entrada.");
        return;
    }

    // Calcular o valor total
    const valorTotal = precoPorNoite * diffDays;
    alert(`Valor total da reserva: R$ ${valorTotal.toFixed(2)} (${diffDays} noites)`);
}

// Adicionar o evento para cada botão "Simular reserva"
document.getElementById('simular-reserva1').addEventListener('click', function() {
    const preco = parseFloat(this.getAttribute('data-preco'));
    const dataEntrada = document.querySelector('input[type="date"]:nth-of-type(1)').value;
    const dataSaida = document.querySelector('input[type="date"]:nth-of-type(2)').value;
    calcularReserva(preco, dataEntrada, dataSaida);
});

// Você pode adicionar os eventos para os outros botões, como simular-reserva2, simular-reserva3 etc.
document.getElementById('simular-reserva2').addEventListener('click', function() {
    const preco = parseFloat(this.getAttribute('data-preco'));
    const dataEntrada = document.querySelector('input[type="date"]:nth-of-type(1)').value;
    const dataSaida = document.querySelector('input[type="date"]:nth-of-type(2)').value;
    calcularReserva(preco, dataEntrada, dataSaida);
});

document.getElementById('simular-reserva3').addEventListener('click', function() {
    const preco = parseFloat(this.getAttribute('data-preco'));
    const dataEntrada = document.querySelector('input[type="date"]:nth-of-type(1)').value;
    const dataSaida = document.querySelector('input[type="date"]:nth-of-type(2)').value;
    calcularReserva(preco, dataEntrada, dataSaida);
});

            // Exibe o botão "Continuar para o Pagamento"
            document.getElementById('proceed-to-payment').style.display = 'inline-block';

            // Armazenar o valor total no localStorage
            localStorage.setItem('total', total.toFixed(2));
        

        // Adiciona o evento ao botão de simular reserva
        document.getElementById('simulate-btn').addEventListener('click', simularReserva);

        // Adiciona o evento ao botão "Continuar para o Pagamento"
        document.getElementById('proceed-to-payment').addEventListener('click', function() {
            window.location.href = "pagamento.html"; // Redireciona para a página de pagamento
        });
            // Função para gerar o resumo e salvar o valor no localStorage
            function gerarResumo() {
                const dailyRate = 390; // Exemplo do valor da diária
                const nights = 3; // Exemplo do número de noites
            
                const total = dailyRate * nights;
            
                // Exibe os dados no resumo
                document.getElementById("summary-nights").textContent = nights;
                document.getElementById("summary-total").textContent = total.toFixed(2);
            
                // Armazena o valor total no localStorage
                localStorage.setItem('total', total.toFixed(2));
            
                // Exemplo de preenchimento da data atual
                const currentDate = new Date();
                document.getElementById("summary-dates").textContent = currentDate.toLocaleDateString("pt-BR");
            }
            
            // Chama a função ao carregar a página
            window.onload = gerarResumo;
            
            // Ao clicar no botão "Continuar para o Pagamento"
            document.getElementById('proceed-to-payment').addEventListener('click', function() {
                window.location.href = "pagamento.html"; // Redireciona para a página de pagamento
            });
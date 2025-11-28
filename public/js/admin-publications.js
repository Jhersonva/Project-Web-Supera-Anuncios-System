//publicaciones.js
const publicaciones = [
{
id: 1,
titulo: "Consultoría de Marketing Digital",
imagen: "https://img.freepik.com/foto-gratis/representacion-3d-interior-moderno-elegante-brillante_181624-43945.jpg",
estado: true,
expira: "2024-03-15"
},
{
id: 2,
titulo: "Clases de Guitarra para Principiantes",
imagen: "https://img.freepik.com/foto-gratis/guitarra-acustica-sobre-fondo-naranja_23-2149870127.jpg",
estado: false,
expira: "2024-02-01"
},
{
id: 3,
titulo: "Venta de Bicicleta de Montaña",
imagen: "https://img.freepik.com/foto-gratis/bicicleta-montana-ciudad-noche_23-2149515828.jpg",
estado: true,
expira: "2024-01-20"
}
];


function cargarPublicaciones() {
const cont = document.getElementById("listaPublicaciones");
cont.innerHTML = "";


publicaciones.forEach(pub => {
cont.innerHTML += `
<div class="pub-card">
<img class="pub-img" src="${pub.imagen}" />


<div class="pub-info">
<div class="pub-titulo">${pub.titulo}</div>


<span class="pub-estado ${pub.estado ? 'estado-activo' : 'estado-inactivo'}">
${pub.estado ? 'Activo' : 'Inactivo'}
</span><br>


<span class="pub-fecha">Expira: ${pub.expira}</span>
</div>


<label class="switch">
<input type="checkbox" ${pub.estado ? 'checked' : ''} onchange="toggleEstado(${pub.id})">
<span class="slider"></span>
</label>
</div>
`;
});
}


function toggleEstado(id) {
const pub = publicaciones.find(p => p.id === id);
pub.estado = !pub.estado;
cargarPublicaciones();
}


cargarPublicaciones();
var map = L.map('map').setView([45.1885, 5.7245], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OSM'
}).addTo(map);

var markers = [];

restaurants.forEach(r => {

    if (r.lat && r.lng) {

        var marker = L.marker([r.lat, r.lng])
        .addTo(map)
        .bindPopup(
            `<b>${r.name}</b><br>
             ${r.address}<br>
             ${r.description || ""}<br>
             📞 ${r.phone || ""}`
        );

        marker.data = r;
        markers.push(marker);
    }
});

// SEARCH
document.getElementById("search").addEventListener("input", function(e) {

    var value = e.target.value.toLowerCase();

    markers.forEach(marker => {

        var match =
            marker.data.name.toLowerCase().includes(value) ||
            marker.data.address.toLowerCase().includes(value);

        if (match) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });
});
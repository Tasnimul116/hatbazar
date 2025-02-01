function fetchCropDetails() {
    console.log("Fetching crop details..."); // Debugging
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log("Response received:", this.responseText); // Debugging
            const data = JSON.parse(this.responseText);
            const dataTable = document.getElementById("dataTable");

            // Clear the table
            dataTable.innerHTML = "";

            // Create a table to display the data
            const table = document.createElement("table");
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Crop Name</th>
                        <th>Quantity (kg)</th>
                        <th>Price (per kg)</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Delivery Time</th>
                        <th>Transaction Method</th>
                    </tr>
                </thead>
                <tbody>
                    ${data
                        .map(
                            (item) => `
                        <tr>
                            <td>${item.cropName}</td>
                            <td>${item.quantity}</td>
                            <td>${item.price}</td>
                            <td>${item.location}</td>
                            <td>${item.contact}</td>
                            <td>${item.deliveryTime}</td>
                            <td>${item.transactionMethod}</td>
                        </tr>
                    `
                        )
                        .join("")}
                </tbody>
            `;

            // Append the table to the dataTable div
            dataTable.appendChild(table);
        }
    };

    xmlhttp.open("GET", "../controllers/fetchCropDetails.php", true);
    xmlhttp.send();
}

// Call fetchCropDetails on page load
document.addEventListener("DOMContentLoaded", fetchCropDetails);
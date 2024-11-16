<!-- resources/views/partials/_numCriteria.blade.php -->

<style>

#criteria-form {
    max-width: 400px;
    margin: 50px auto; /* Center the form and add top margin */
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
}

#criteria-form label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

#criteria-form input[type="number"] {
    width: 80%;
    padding: 10px;
    align-self: center;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#criteria-form button {
    width: 50%;
    padding: 10px;
    align-self: center;
    background-color: #333;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

#criteria-form button:hover {
    background-color: #0056b3;
}

#dynamic-content p {
    text-align: center;
    font-size: 16px;
    color: #333;
}

</style>

<form id="criteria-form">
    <label for="criteria">What is the number of criteria?</label>
    <input type="number" id="criteria" name="criteria" required>
    <button type="submit">Next</button>
</form>
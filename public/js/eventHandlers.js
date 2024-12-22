document.addEventListener('DOMContentLoaded', () => {
    const criteriaForm = document.getElementById('criteria-form');
    if (criteriaForm) {
        criteriaForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            console.log('Criteria form submitted');

            const problemName = document.getElementById('problem-name').value;
            const criteria = document.getElementById('criteria').value;
            if (!validateCriteriaInput(criteria)) return;

            document.getElementById('dynamic-content').innerHTML = '<p>Loading...</p>';

            try {
                const data = await sendCriteria(problemName, criteria);
                if (data.html) {
                    document.getElementById('dynamic-content').innerHTML = data.html;
                    attachCriteriaFormListener();
                } else {
                    document.getElementById('dynamic-content').innerHTML = '<p>No content available.</p>';
                }
            } catch (error) {
                console.error('Error details:', error);
                document.getElementById('dynamic-content').innerHTML = '<p>An error occurred. Please try again later.</p>';
            }
        });
    } else {
        console.error('Criteria form not found');
    }
});

function attachCriteriaFormListener() {
    const criteriaNamesWeightsForm = document.getElementById('criteria-names-weights-form');
    if (criteriaNamesWeightsForm) {
        criteriaNamesWeightsForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            console.log('Criteria names and weights form submitted');

            const criteriaNames = Array.from(document.querySelectorAll('input[name="criteria_names[]"]')).map(input => input.value.trim());
            const criteriaWeights = Array.from(document.querySelectorAll('input[name="criteria_weights[]"]')).map(input => parseFloat(input.value));
            const intervals = Array.from(document.querySelectorAll('[id^="intervals_"]')).map(group => {
                const intervalArray = [];
                group.querySelectorAll('input').forEach(input => {
                    const value = parseFloat(input.value);
                    intervalArray.push(value);
                });
                return intervalArray;
            });

            console.log('Criteria Names:', criteriaNames);
            console.log('Criteria Weights:', criteriaWeights);
            console.log('Intervals:', intervals);

            if (!validateCriteriaNames(criteriaNames) || !validateCriteriaWeights(criteriaWeights) || !validateIntervals(intervals)) {
                console.log('Validation failed');
                return;
            }

            try {
                const response = await sendCriteriaDetails(criteriaNames, criteriaWeights, intervals);
                console.log('Response:', response);
                if (response.status === 422) {
                    const data = await response.json();
                    console.error('Validation errors:', data.errors);
                    alert('Validation errors occurred. Please check your input.');
                    return;
                }

                if (response.redirected) {
                    console.log('Redirecting to:', response.url);
                    window.location.href = response.url;
                } else {
                    const html = await response.text();
                    console.log('Received HTML content:', html);
                    document.getElementById('dynamic-content').innerHTML = html;
                }
            } catch (error) {
                console.error('Error details:', error);
                alert('An error occurred. Please try again.');
            }
        });
    } else {
        console.error('Criteria names and weights form not found');
    }
}



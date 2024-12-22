async function sendCriteria(problemName, criteria) {
    const response = await fetch(window.Laravel.routes.criteriaSubmit, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        body: JSON.stringify({ problem_name: problemName, criteria })
    });
    return response.json();
}

async function sendCriteriaDetails(criteriaNames, criteriaWeights, intervals) {
    const response = await fetch(window.Laravel.routes.storeCriteriaNamesWeights, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        body: JSON.stringify({ criteria_names: criteriaNames, criteria_weights: criteriaWeights, intervals })
    });
    return response;
}
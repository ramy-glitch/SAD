function validateCriteriaInput(criteria) {
    if (!criteria || isNaN(criteria) || criteria <= 1) {
        alert('Please enter a valid number of criteria.');
        return false;
    }
    return true;
}

function validateCriteriaNames(criteriaNames) {
    const seenNames = new Set();
    for (let name of criteriaNames) {
        if (!name.trim()) {
            alert('Criterion names cannot be empty.');
            return false;
        }
        if (seenNames.has(name)) {
            alert('Criterion names must not be repeated.');
            return false;
        }
        seenNames.add(name);
    }
    return true;
}

function validateCriteriaWeights(criteriaWeights) {
    let sum = 0;
    for (let weight of criteriaWeights) {
        if (isNaN(weight) || weight < 0.01 || weight > 1) {
            alert('Criterion weights must be between 0.01 and 1.');
            return false;
        }
        sum += weight;
    }
    if (sum !== 1) {
        alert('The sum of criterion weights must equal 1.');
        return false;
    }
    return true;
}

function validateIntervals(intervals) {
    for (let group of intervals) {
        if (group.length !== 10) {
            alert('Each interval group must contain exactly 10 values.');
            return false;
        }
        for (let value of group) {
            if (isNaN(value)) {
                alert('Interval values must be numeric.');
                return false;
            }
        }
    }
    return true;
}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Employee Details</h1>

        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">{{ $employee->name }}</h4>
                <p class="card-text"><strong>Contact Number:</strong> {{ $employee->contact_number }}</p>
                <p class="card-text"><strong>Department:</strong>
                    {{ $employee->department ? $employee->department->name : 'No Department Assigned' }}
                </p>
            </div>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">Back to Employee List</a>
        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning mt-3">Edit</a>
        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</button>
        </form>
    </div>
</body>

</html>
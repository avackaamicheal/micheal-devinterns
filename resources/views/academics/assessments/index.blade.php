@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Assessment Configuration</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ resolveRoute('assessments.index') }}" method="GET" class="form-inline">
                            <label class="mr-3">Select Subject:</label>
                            <select name="subject_id" class="form-control mr-3" required>
                                <option value="">-- Choose Subject --</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Load Configuration</button>
                        </form>
                    </div>
                </div>

                @if ($selectedSubject)
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Grading Formula for: <strong>{{ $selectedSubject->name }}</strong></h3>
                        </div>

                        <form action="{{ resolveRoute('assessments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">

                            <div class="card-body">
                                <table class="table table-bordered" id="dynamicTable">
                                    <thead>
                                        <tr>
                                            <th>Assessment Category Name (e.g., Homework, Final Exam)</th>
                                            <th style="width: 200px;">Weight (%)</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($weights as $index => $weight)
                                            <tr>
                                                <td><input type="text" name="categories[{{ $index }}][name]"
                                                        value="{{ $weight->name }}" class="form-control" required></td>
                                                <td><input type="number" name="categories[{{ $index }}][weight]"
                                                        value="{{ $weight->weight }}" class="form-control weight-input"
                                                        required></td>
                                                <td><button type="button" class="btn btn-danger remove-tr"><i
                                                            class="fas fa-trash"></i></button></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td><input type="text" name="categories[0][name]"
                                                        placeholder="e.g., Final Exam" class="form-control" required></td>
                                                <td><input type="number" name="categories[0][weight]" placeholder="100"
                                                        class="form-control weight-input" required></td>
                                                <td><button type="button" class="btn btn-danger remove-tr"><i
                                                            class="fas fa-trash"></i></button></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td class="text-right font-weight-bold">Total Weight:</td>
                                            <td class="font-weight-bold"><span id="totalWeightDisplay">0</span>%</td>
                                            <td><button type="button" name="add" id="add"
                                                    class="btn btn-success"><i class="fas fa-plus"></i> Add</button></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> The total weight must sum to
                                    exactly 100% before you can save.</small>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">Save Configuration</button>
                            </div>
                        </form>
                    </div>
                @endif

            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let i = {{ $weights->count() > 0 ? $weights->count() : 1 }};

            // Add new row
            document.getElementById('add').addEventListener('click', function() {
                let html = `<tr>
                <td><input type="text" name="categories[${i}][name]" placeholder="Enter Category" class="form-control" required></td>
                <td><input type="number" name="categories[${i}][weight]" placeholder="0" class="form-control weight-input" required></td>
                <td><button type="button" class="btn btn-danger remove-tr"><i class="fas fa-trash"></i></button></td>
            </tr>`;
                document.querySelector('#dynamicTable tbody').insertAdjacentHTML('beforeend', html);
                i++;
                calculateTotal();
            });

            // Remove row
            document.querySelector('#dynamicTable').addEventListener('click', function(e) {
                if (e.target.closest('.remove-tr')) {
                    e.target.closest('tr').remove();
                    calculateTotal();
                }
            });

            // Calculate total when typing
            document.querySelector('#dynamicTable').addEventListener('input', function(e) {
                if (e.target.classList.contains('weight-input')) {
                    calculateTotal();
                }
            });

            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('.weight-input').forEach(function(input) {
                    total += parseInt(input.value) || 0;
                });

                let display = document.getElementById('totalWeightDisplay');
                display.innerText = total;

                // Turn text red if it's over or under 100
                display.className = (total === 100) ? 'text-success' : 'text-danger';
            }

            calculateTotal(); // Run once on page load
        });
    </script>
@endsection

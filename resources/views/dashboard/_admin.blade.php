<div class="row mt-3">
    <div class="col-lg-6">
        <section class="card h-100" aria-labelledby="admin-users">
            <div class="card-body">
                <h2 id="admin-users" class="h5">Users by role</h2>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Role</th>
                                <th scope="col">Users</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\User::ROLES as $role)
                                <tr>
                                    <td>{{ ucfirst($role) }}</td>
                                    <td>{{ $roleCounts[$role] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.users.index') }}" class="d-inline-block mt-2">Manage users</a>
            </div>
        </section>
    </div>

    <div class="col-lg-6">
        <section class="card h-100" aria-labelledby="admin-totals">
            <div class="card-body">
                <h2 id="admin-totals" class="h5">Totals</h2>
                <p class="mb-1">Modules and study guides: <strong>{{ $moduleCount }}</strong></p>
                <p class="mb-1">Quizzes: <strong>{{ $quizCount }}</strong></p>
                <p class="mb-3">Forum threads: <strong>{{ $threadCount }}</strong></p>

                <p class="mb-2">
                    Postings waiting for approval: <strong>{{ $pendingCount }}</strong>
                    <a href="{{ route('admin.opportunities.index') }}">Review</a>
                </p>
                <a href="{{ route('admin.links.index') }}">Link parents to students</a>
            </div>
        </section>
    </div>
</div>

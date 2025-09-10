@extends('layouts.app')

@section('title', 'CRM Modules')

@section('content')
<div class="container py-4">

  <h2 class="mb-4 fw-bold">CRM</h2>

  <div class="row g-3">

    @php
    $modules = [
      ['icon' => 'bi-people-fill', 'title' => 'Lead Management', 'desc' => 'Streamline capture, qualification, and conversion.','route' => 'crm/lead-management'],
      ['icon' => 'bi-trophy', 'title' => 'Opportunity Management', 'desc' => 'Track deals, forecast revenue, and close efficiently.'],
      ['icon' => 'bi-list-task', 'title' => 'Contact Management', 'desc' => '360° customer view with centralized information.'],

      ['icon' => 'bi-building', 'title' => 'Company Management', 'desc' => 'Manage accounts, profiles, and interactions.'],
      ['icon' => 'bi-graph-up-arrow', 'title' => 'Forecast Management', 'desc' => 'Data-driven sales forecasting and planning.'],
      ['icon' => 'bi-box-seam', 'title' => 'Product Management', 'desc' => 'Catalog, pricing, and inventory for smooth sales.'],

      ['icon' => 'bi-file-earmark-text', 'title' => 'Quotation Management', 'desc' => 'Create & track professional quotes.'],
      ['icon' => 'bi-card-list', 'title' => 'Sales Order Management', 'desc' => 'Convert quotes to orders and fulfill.'],
      ['icon' => 'bi-receipt', 'title' => 'Invoicing Management', 'desc' => 'Generate invoices, track payments, cash-flow.'],

      ['icon' => 'bi-cart-check', 'title' => 'Purchase Order Management', 'desc' => 'Create POs, approvals, and vendor tracking.'],
      ['icon' => 'bi-credit-card', 'title' => 'Billing Management', 'desc' => 'Subscriptions, recurring invoices, reminders.'],
      ['icon' => 'bi-cash-stack', 'title' => 'Expenses Management', 'desc' => 'Record & control business expenses.'],

      ['icon' => 'bi-person-check', 'title' => 'Approval Processes', 'desc' => 'Automated approvals & rules.'],
      ['icon' => 'bi-bar-chart', 'title' => 'Reports Management', 'desc' => 'Powerful, exportable BI reports.'],
      ['icon' => 'bi-pie-chart', 'title' => 'Analytics Management', 'desc' => 'Reveal trends with advanced analytics.'],

      ['icon' => 'bi-check-circle', 'title' => 'Task Management', 'desc' => 'Assign tasks, track progress, hit deadlines.'],
    ];
    @endphp

@foreach ($modules as $module)
  <div class="col-12 col-md-6 col-lg-4">
    <a href="{{ isset($module['route']) ? route($module['route']) : '#' }}" class="text-decoration-none text-reset">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-start gap-3">
          <i class="bi {{ $module['icon'] }} fs-3 text-primary flex-shrink-0"></i>
          <div>
            <h6 class="card-title fw-bold mb-1">{{ $module['title'] }}</h6>
            <p class="card-text text-muted mb-0" style="font-size: 0.9rem;">{{ $module['desc'] }}</p>
          </div>
        </div>
      </div>
    </a>
  </div>
@endforeach



  </div>

</div>
@endsection

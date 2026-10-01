<div class="document-container">
    <!-- Header Section -->
    @include('documents.partials.header')

    <!-- Patient Info Section -->
    @include('documents.partials.patient-info')

    <!-- Content Section -->
    {{ $content ?? '' }}

    <!-- Footer Section with Signature and Seal -->
    @include('documents.partials.footer')
</div>

@extends('template')

@section('contents')
<style>
    /* Custom styles for notes visibility */
    .note-fields {
        font-size: 1rem;
        max-height: 350px;
        overflow-y: auto;
        border: 1px solid #eaeaea;
        border-radius: 4px;
        padding: 8px;
        background-color: #fcfcfc;
    }
    
    .note-fields .mb-2 {
        background-color: white;
        border-radius: 4px;
        padding: 8px !important;
        margin-bottom: 10px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .note-fields .fw-bold {
        font-size: 1.05rem;
        color: #3f51b5 !important;
    }
    
    .note-fields .ms-2 {
        font-size: 1rem;
        padding: 4px 0;
        word-break: break-word;
    }
    
    /* All Notes Modal Enhancements */
    #viewNotesModal .card {
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    #viewNotesModal .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    #viewNotesModal .table-sm {
        font-size: 0.95rem;
    }
    
    #viewNotesModal .table-sm td {
        padding: 0.5rem 0.75rem;
        vertical-align: middle;
    }
    
    #viewNotesModal .text-primary {
        color: #3f51b5 !important;
    }
    
    #viewNotesModal .note-content {
        background-color: #fff;
        border-radius: 0.25rem;
    }
    
    #viewNotesModal .badge {
        font-weight: 500;
        padding: 0.4em 0.6em;
    }
    
    #viewNotesModal .accordion-button {
        background-color: #f8f9fa;
    }
    
    #viewNotesModal .accordion-button:not(.collapsed) {
        background-color: #e9ecef;
        color: #3f51b5;
    }
    
    #viewNotesModal dl.row dt {
        font-weight: 600;
    }
    
    #viewNotesModal dl.row dd {
        margin-bottom: 0.5rem;
    }
    
    #notes-list-view .table td {
        vertical-align: middle;
    }
    
    /* Improve content readability */
    .toggle-content {
        font-size: 0.85rem;
        text-decoration: none;
    }
    
    /* Make buttons more visible */
    .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    /* Add visual emphasis to tables */
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Improve modal appearance */
    .modal-content {
        border-radius: 6px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .modal-header.bg-primary {
        background-color: #3f51b5 !important;
    }
    
    /* Improve mobile responsiveness */
    @media (max-width: 768px) {
        .table td, .table th {
            padding: 0.5rem;
        }
        
        .note-fields {
            max-height: 250px;
        }
        
        #viewNotesModal .col-md-6 {
            margin-bottom: 1rem;
        }
    }
</style>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Company Details</h4>
                    <div>
                        <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#viewNotesModal">
                            <i class="mdi mdi-eye"></i> View Notes
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="mdi mdi-note-plus"></i> Add Note
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title" id="companyName">{{ $company->name }}</h5>
                    <p class="card-text"><strong>Contact:</strong> {{ $company->contact ?? '-' }}</p>
                    <p class="card-text"><strong>Address:</strong> {{ $company->address ?? '-' }}</p>
                    <p class="card-text"><strong>URL:</strong> {!! $company->url ? '<a href="' . $company->url . '" target="_blank">' . $company->url . '</a>' : '-' !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notes ({{ $notes->count() }})</h5>
                    @if($notes->count())
                        <span class="text-muted small">First note: {{ $notes->last()->created_at->diffForHumans() }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($notes->count())
                        <div class="accordion" id="notesAccordion">
                            @foreach($notes as $index => $note)
                                <div class="accordion-item mb-3 border">
                                    <h2 class="accordion-header" id="heading{{ $note->id }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $note->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $note->id }}">
                                            <span class="me-2">Note from {{ $note->created_at->format('M d, Y') }}</span>
                                            <span class="badge bg-primary me-2">{{ count((array)$note->data) }} fields</span>
                                            <small class="text-muted">by {{ $note->user->name }}</small>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $note->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                         aria-labelledby="heading{{ $note->id }}" data-bs-parent="#notesAccordion">
                                        <div class="accordion-body">
                                            @if(is_array($note->data))
                                                @if(isset($note->data[0]) && is_array($note->data[0]))
                                                    <!-- New format: array of objects -->
                                                    <div class="row">
                                                        @foreach($note->data as $field)
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card h-100">
                                                                    <div class="card-header py-2 bg-light">
                                                                        <strong>{{ $field['label'] ?? 'Unknown' }}</strong>
                                                                    </div>
                                                                    <div class="card-body py-2">
                                                                        <p class="mb-1">{{ $field['value'] ?? '' }}</p>
                                                                        @if(isset($field['secondValue']) && !empty($field['secondValue']))
                                                                            <p class="mb-0 text-muted small">
                                                                                <strong>Additional:</strong> {{ $field['secondValue'] }}
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <!-- Legacy format handling -->
                                                    <div class="row">
                                                        @foreach($note->data as $label => $field)
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card h-100">
                                                                    <div class="card-header py-2 bg-light">
                                                                        <strong>{{ $label }}</strong>
                                                                    </div>
                                                                    <div class="card-body py-2">
                                                                        <p class="mb-1">{{ is_array($field) ? $field['value'] : $field }}</p>
                                                                        @if(is_array($field) && isset($field['secondValue']) && !empty($field['secondValue']))
                                                                            <p class="mb-0 text-muted small">
                                                                                <strong>Additional:</strong> {{ $field['secondValue'] }}
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @else
                                                <div class="alert alert-warning">Invalid note data format</div>
                                            @endif
                                            
                                            <div class="mt-3 d-flex justify-content-end">
                                                <button class="btn btn-sm btn-info edit-note me-2" 
                                                        data-note-id="{{ $note->id }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editNoteModal">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('notes.destroy', $note->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this note?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Time since last note:</strong>
                            @php
                                $lastNote = $notes->first();
                                echo $lastNote ? $lastNote->created_at->diffForHumans() : 'N/A';
                            @endphp
                        </div>
                    @else
                        <div class="alert alert-warning">No notes found for this company.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- View Notes Modal -->
    <div class="modal fade" id="viewNotesModal" tabindex="-1" aria-labelledby="viewNotesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewNotesModalLabel">
                        <i class="mdi mdi-note-text me-1"></i> All Notes for {{ $company->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($notes->count() > 0)
                        <!-- Note management header with filters and actions -->
                        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="mb-2">
                                <div class="text-muted small mb-1">
                                    <strong>Total Notes:</strong> {{ $notes->count() }}
                                </div>
                                <div class="btn-group btn-group-sm" role="group" aria-label="View Options">
                                    <button type="button" class="btn btn-outline-primary active" id="view-cards">
                                        <i class="mdi mdi-view-grid"></i> Cards
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="view-list">
                                        <i class="mdi mdi-view-list"></i> List
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <button class="btn btn-sm btn-success" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                    <i class="mdi mdi-plus"></i> Add New Note
                                </button>
                            </div>
                        </div>
                        
                        <!-- Cards View (default) -->
                        <div id="notes-cards-view">
                            <div class="row">
                                @foreach($notes as $note)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 border-primary border-top border-3">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                                <span class="text-dark" title="Created {{ $note->created_at->format('M d, Y H:i') }}">
                                                    <i class="mdi mdi-calendar"></i> {{ $note->created_at->format('M d, Y') }}
                                                </span>
                                                <span class="badge bg-info">{{ count((array)$note->data) }} fields</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="note-content p-3" style="max-height: 200px; overflow-y: auto;">
                                                    @if(is_array($note->data))
                                                        @if(isset($note->data[0]) && is_array($note->data[0]))
                                                            <!-- New format: array of objects -->
                                                            <div class="table-responsive">
                                                                <table class="table table-sm mb-0">
                                                                    <tbody>
                                                                        @foreach($note->data as $field)
                                                                            <tr>
                                                                                <td class="fw-bold text-primary" style="width: 40%">{{ $field['label'] ?? 'Unknown' }}:</td>
                                                                                <td>
                                                                                    {{ $field['value'] ?? '' }}
                                                                                    @if(isset($field['secondValue']) && !empty($field['secondValue']))
                                                                                        <div class="text-muted small">{{ $field['secondValue'] }}</div>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <!-- Legacy format: object with key-value pairs -->
                                                            <div class="table-responsive">
                                                                <table class="table table-sm mb-0">
                                                                    <tbody>
                                                                        @foreach($note->data as $label => $field)
                                                                            <tr>
                                                                                <td class="fw-bold text-primary" style="width: 40%">{{ $label }}:</td>
                                                                                <td>
                                                                                    {{ is_array($field) ? $field['value'] : $field }}
                                                                                    @if(is_array($field) && isset($field['secondValue']) && !empty($field['secondValue']))
                                                                                        <div class="text-muted small">{{ $field['secondValue'] }}</div>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="alert alert-warning">Invalid note data format</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-dark">
                                                        <i class="mdi mdi-account"></i> {{ $note->user->name }}
                                                    </small>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-info edit-note" 
                                                                data-note-id="{{ $note->id }}"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editNoteModal">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('notes.destroy', $note->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this note?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- List View (hidden by default) -->
                        <div id="notes-list-view" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 15%">Date</th>
                                            <th style="width: 65%">Content</th>
                                            <th style="width: 10%">Created By</th>
                                            <th style="width: 10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notes as $note)
                                            <tr>
                                                <td class="text-dark fw-normal">{{ $note->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="accordion accordion-flush" id="listAccordion{{ $note->id }}">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed p-1" type="button" 
                                                                        data-bs-toggle="collapse" 
                                                                        data-bs-target="#flush-collapse{{ $note->id }}">
                                                                    <span class="badge bg-info me-2">{{ count((array)$note->data) }} fields</span>
                                                                    <span>Click to view details</span>
                                                                </button>
                                                            </h2>
                                                            <div id="flush-collapse{{ $note->id }}" class="accordion-collapse collapse" 
                                                                data-bs-parent="#listAccordion{{ $note->id }}">
                                                                <div class="accordion-body p-2">
                                                                    @if(is_array($note->data))
                                                                        @if(isset($note->data[0]) && is_array($note->data[0]))
                                                                            <dl class="row mb-0">
                                                                                @foreach($note->data as $field)
                                                                                    <dt class="col-sm-3 text-primary">{{ $field['label'] ?? 'Unknown' }}:</dt>
                                                                                    <dd class="col-sm-9">
                                                                                        {{ $field['value'] ?? '' }}
                                                                                        @if(isset($field['secondValue']) && !empty($field['secondValue']))
                                                                                            <small class="text-muted d-block">{{ $field['secondValue'] }}</small>
                                                                                        @endif
                                                                                    </dd>
                                                                                @endforeach
                                                                            </dl>
                                                                        @else
                                                                            <dl class="row mb-0">
                                                                                @foreach($note->data as $label => $field)
                                                                                    <dt class="col-sm-3 text-primary">{{ $label }}:</dt>
                                                                                    <dd class="col-sm-9">
                                                                                        {{ is_array($field) ? $field['value'] : $field }}
                                                                                        @if(is_array($field) && isset($field['secondValue']) && !empty($field['secondValue']))
                                                                                            <small class="text-muted d-block">{{ $field['secondValue'] }}</small>
                                                                                        @endif
                                                                                    </dd>
                                                                                @endforeach
                                                                            </dl>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-dark fw-normal">{{ $note->user->name }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info edit-note mb-1" 
                                                            data-note-id="{{ $note->id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editNoteModal">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('notes.destroy', $note->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this note?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="mdi mdi-information-outline me-2"></i>
                            No notes found for this company. Click the "Add Note" button to create the first note.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        <i class="mdi mdi-plus"></i> Add New Note
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addNoteModalLabel">Add Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('notes.store') }}" id="noteForm">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            Adding note for: <strong>{{ $company->name }}</strong>
                        </div>
                        <div id="customFieldsContainer" class="bg-light p-3 rounded">
                            <!-- Dynamic fields will be added here -->
                        </div>
                        <button type="button" class="btn btn-info mt-3" id="addCustomFieldBtn">
                            <i class="mdi mdi-plus"></i> Add Field
                        </button>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Note Modal -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editNoteModalLabel">Edit Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editNoteForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div id="editCustomFieldsContainer" class="bg-light p-3 rounded">
                            <!-- Dynamic fields will be added here -->
                        </div>
                        <button type="button" class="btn btn-info mt-3" id="editAddCustomFieldBtn">
                            <i class="mdi mdi-plus"></i> Add Field
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('admin_lang/lang/javascript/add_note.js') }}"></script>
<script>
$(document).ready(function() {
    var currentCompanyId = "{{ $company->id }}";
    console.log('Company dashboard loaded for company ID:', currentCompanyId);
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // View toggle handlers for all notes modal
    $('#view-cards').click(function() {
        $(this).addClass('active');
        $('#view-list').removeClass('active');
        $('#notes-cards-view').removeClass('d-none');
        $('#notes-list-view').addClass('d-none');
    });
    
    $('#view-list').click(function() {
        $(this).addClass('active');
        $('#view-cards').removeClass('active');
        $('#notes-list-view').removeClass('d-none');
        $('#notes-cards-view').addClass('d-none');
    });
    
    // Initialize modals with dynamic content
    $('#viewNotesModal').on('shown.bs.modal', function () {
        // Refresh card heights to ensure consistent sizing
        setTimeout(function() {
            equalizeCardHeights();
        }, 100);
    });
    
    // Function to equalize card heights for better appearance
    function equalizeCardHeights() {
        // Reset heights first
        $('.card-body .note-content').css('height', 'auto');
        
        // Get cards in each row and set equal heights
        var cardGroups = {};
        $('.card').each(function() {
            var top = $(this).offset().top;
            if (!cardGroups[top]) cardGroups[top] = [];
            cardGroups[top].push($(this));
        });
        
        // Set heights for each row
        $.each(cardGroups, function(key, cards) {
            var maxHeight = 0;
            $.each(cards, function(i, card) {
                var contentHeight = card.find('.note-content').height();
                maxHeight = Math.max(maxHeight, contentHeight);
            });
            
            // Apply the max height to all cards in this row
            if (maxHeight > 0) {
                $.each(cards, function(i, card) {
                    card.find('.note-content').height(maxHeight);
                });
            }
        });
    }
    
    // Handle content truncation for better display
    $('.note-content').each(function() {
        var $content = $(this);
        
        // Process each table cell with content
        $content.find('td:nth-child(2)').each(function() {
            var $cell = $(this);
            var text = $cell.text().trim();
            
            // If content is long, truncate it
            if (text.length > 80) {
                var shortText = text.substring(0, 80) + '...';
                var fullText = text;
                
                // Create wrapped content with toggle
                var $shortVersion = $('<span class="content-short">' + shortText + ' </span>');
                var $fullVersion = $('<span class="content-full d-none">' + fullText + ' </span>');
                var $toggle = $('<a href="#" class="toggle-content text-primary">more</a>');
                
                // Clear and append new content
                $cell.html('').append($shortVersion).append($fullVersion).append($toggle);
                
                // Add toggle functionality
                $toggle.click(function(e) {
                    e.preventDefault();
                    if ($shortVersion.hasClass('d-none')) {
                        $shortVersion.removeClass('d-none');
                        $fullVersion.addClass('d-none');
                        $toggle.text('more');
                    } else {
                        $fullVersion.removeClass('d-none');
                        $shortVersion.addClass('d-none');
                        $toggle.text('less');
                    }
                });
            }
        });
    });
    
    // Make sure accordion is initialized
    var accordionElement = document.getElementById('notesAccordion');
    if (accordionElement) {
        // Add hover effect to accordion items
        $('.accordion-item').hover(
            function() { $(this).addClass('shadow-sm'); },
            function() { $(this).removeClass('shadow-sm'); }
        );
        
        // Make text more readable by setting minimum height
        $('.card-body p').css('min-height', '20px');
        
        // Add ellipsis for long text values
        $('.card-body p').each(function() {
            var $this = $(this);
            if ($this.text().length > 100) {
                var shortText = $this.text().substr(0, 100) + '...';
                var fullText = $this.text();
                
                $this.html(shortText)
                    .append($('<a href="#" class="ms-1 text-primary">more</a>')
                        .click(function(e) {
                            e.preventDefault();
                            if ($this.html().indexOf('more') >= 0) {
                                $this.html(fullText)
                                    .append($('<a href="#" class="ms-1 text-primary">less</a>')
                                        .click(function(e) {
                                            e.preventDefault();
                                            $this.html(shortText)
                                                .append($('<a href="#" class="ms-1 text-primary">more</a>')
                                                    .click(arguments.callee));
                                        }));
                            }
                        }));
            }
        });
    }
    
    // Function to add a custom field
    function addCustomField(label = '', value = '', secondValue = '', type = 'text', container = '#customFieldsContainer') {
        var hasSecondValue = secondValue !== '';
        $(container).append(`
            <div class="row g-2 align-items-center custom-field-row mb-2">
                <div class="col-3">
                    <select name="custom_types[]" class="form-select custom-type-select">
                        <option value="text" ${type === 'text' ? 'selected' : ''}>Text</option>
                        <option value="number" ${type === 'number' ? 'selected' : ''}>Number</option>
                        <option value="date" ${type === 'date' ? 'selected' : ''}>Date</option>
                        <option value="email" ${type === 'email' ? 'selected' : ''}>Email</option>
                        <option value="url" ${type === 'url' ? 'selected' : ''}>URL</option>
                        <option value="custom" ${type === 'custom' ? 'selected' : ''}>Custom</option>
                    </select>
                </div>
                <div class="col-3">
                    <input type="text" name="custom_labels[]" class="form-control custom-label-input" placeholder="Label" value="${label}" required>
                </div>
                <div class="col-4">
                    <input type="text" name="custom_values[]" class="form-control custom-value-input" placeholder="Value" value="${value}" required>
                </div>
                <div class="col-2 second-value-container" style="${hasSecondValue ? 'display: block' : 'display: none'}">
                    <input type="text" name="custom_second_values[]" class="form-control custom-second-value-input" placeholder="Second Value" value="${secondValue}">
                </div>
                <div class="col-2 d-flex align-items-center">
                    <button type="button" class="btn btn-info btn-sm addSecondInputBtn" title="${hasSecondValue ? 'Remove Second Input' : 'Add Second Input'}">
                        <i class="mdi ${hasSecondValue ? 'mdi-minus' : 'mdi-plus'}"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm removeCustomFieldBtn ms-1" title="Remove Field">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>
        `);
    }
    
    // Add initial field
    addCustomField();

    // Add field button click handler
    $('#addCustomFieldBtn').click(function() {
        addCustomField();
    });

    // Remove field button click handler
    $(document).on('click', '.removeCustomFieldBtn', function() {
        $(this).closest('.custom-field-row').remove();
    });

    // Toggle second input field
    $(document).on('click', '.addSecondInputBtn', function() {
        var row = $(this).closest('.custom-field-row');
        var secondValueContainer = row.find('.second-value-container');
        var icon = $(this).find('i');
        var valueCol = row.find('.custom-value-input').closest('div');
        
        if (secondValueContainer.is(':visible')) {
            secondValueContainer.hide();
            icon.removeClass('mdi-minus').addClass('mdi-plus');
            $(this).attr('title', 'Add Second Input');
            $(this).removeClass('btn-danger').addClass('btn-info');
            
            // Adjust the size of the value input column
            if (valueCol.hasClass('col-4')) {
                valueCol.removeClass('col-4').addClass('col-6');
            }
            
            // Clear the second value
            secondValueContainer.find('input').val('');
        } else {
            secondValueContainer.show();
            icon.removeClass('mdi-plus').addClass('mdi-minus');
            $(this).attr('title', 'Remove Second Input');
            $(this).removeClass('btn-info').addClass('btn-danger');
            
            // Adjust the size of the value input column
            if (valueCol.hasClass('col-6')) {
                valueCol.removeClass('col-6').addClass('col-4');
            }
        }
    });

    // Override the form submission to make sure it properly redirects
    $('#noteForm').submit(function(e) {
        e.preventDefault();
        
        // Create form data object
        var formData = {
            company_id: currentCompanyId,
            _token: $('meta[name="csrf-token"]').attr('content'),
            data: []
        };
        
        // Get all rows from the current form
        var hasValidFields = false;
        $(this).find('.custom-field-row').each(function() {
            var row = $(this);
            var type = row.find('.custom-type-select').val();
            var label = row.find('.custom-label-input').val();
            var value = row.find('.custom-value-input').val();
            var secondValueContainer = row.find('.second-value-container');
            var secondValue = '';

            // Clear previous validation styling
            row.find('input').removeClass('is-invalid');
            
            // Only include second value if the container is visible
            if (secondValueContainer.is(':visible')) {
                secondValue = row.find('.custom-second-value-input').val() || '';
            }

            // Only add if label and value are not empty
            if (label && value) {
                hasValidFields = true;
                var fieldData = {
                    type: type || 'text',
                    value: value,
                    label: label
                };

                if (secondValue) {
                    fieldData.secondValue = secondValue;
                }
                
                formData.data.push(fieldData);
            }
        });

        // Validate fields
        if (!hasValidFields) {
            alert('Please add at least one field with label and value');
            $(this).find('.custom-label-input:first, .custom-value-input:first').addClass('is-invalid');
            return false;
        }

        // Disable the submit button
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: "{{ route('notes.store') }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Form submission success:', response);
                if (response.success) {
                    // Show success message
                    alert('Note saved successfully!');
                    // Close the modal
                    $('#addNoteModal').modal('hide');
                    // Reload the page to see the updated notes
                    window.location.reload();
                } else {
                    handleFormError(response, submitButton);
                }
            },
            error: function(xhr, status, error) {
                handleFormError(xhr.responseJSON, submitButton);
            }
        });

        return false;
    });

    // Helper function to handle form errors
    function handleFormError(response, submitButton) {
        var errorMessage = 'Error saving note: ';
        
        if (response && response.errors) {
            errorMessage += '\n' + Object.entries(response.errors)
                .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                .join('\n');
        } else if (response && response.message) {
            errorMessage += response.message;
        } else {
            errorMessage += 'An unknown error occurred';
        }
        
        alert(errorMessage);
        submitButton.prop('disabled', false);
    }

    // Handle edit button click
    $('.edit-note').click(function(e) {
        e.preventDefault();
        var noteId = $(this).data('note-id');
        var form = $('#editNoteForm');
        
        // Clear previous fields
        $('#editCustomFieldsContainer').empty();
        
        // Set form action URL
        form.attr('action', `/notes/${noteId}`);
        
        // Fetch note data
        $.ajax({
            url: `/notes/${noteId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    var note = response.note;
                    
                    // Add fields - for array format
                    if (note.data && Array.isArray(note.data)) {
                        note.data.forEach(field => {
                            addCustomField(
                                field.label || '',
                                field.value || '',
                                field.secondValue || '',
                                field.type || 'custom',
                                '#editCustomFieldsContainer'
                            );
                        });
                    } 
                    // For legacy object format
                    else if (note.data) {
                        Object.entries(note.data).forEach(([label, field]) => {
                            var value = typeof field === 'object' ? field.value : field;
                            var secondValue = typeof field === 'object' ? field.secondValue || '' : '';
                            var type = typeof field === 'object' ? field.type || 'custom' : 'custom';
                            
                            addCustomField(
                                label,
                                value,
                                secondValue,
                                type,
                                '#editCustomFieldsContainer'
                            );
                        });
                    }
                }
            },
            error: function(xhr) {
                console.error('Error fetching note:', xhr);
                alert('Error fetching note details. Please try again.');
            }
        });
    });

    // Add field button for edit modal
    $('#editAddCustomFieldBtn').click(function() {
        addCustomField('', '', '', 'custom', '#editCustomFieldsContainer');
    });

    // Edit note form submission
    $('#editNoteForm').submit(function(e) {
        e.preventDefault();
        
        var form = $(this);
        var action = form.attr('action');
        var submitButton = form.find('button[type="submit"]');
        
        // Create form data object
        var formData = {
            company_id: currentCompanyId, // Use the current company ID
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT',
            data: []
        };
        
        // Get all rows from the form
        var hasValidFields = false;
        form.find('.custom-field-row').each(function() {
            var row = $(this);
            var type = row.find('.custom-type-select').val();
            var label = row.find('.custom-label-input').val();
            var value = row.find('.custom-value-input').val();
            var secondValueContainer = row.find('.second-value-container');
            var secondValue = '';
            
            // Only include second value if the container is visible
            if (secondValueContainer.is(':visible')) {
                secondValue = row.find('.custom-second-value-input').val() || '';
            }

            if (label && value) {
                hasValidFields = true;
                var fieldData = {
                    type: type || 'text',
                    value: value,
                    label: label
                };

                if (secondValue) {
                    fieldData.secondValue = secondValue;
                }
                
                formData.data.push(fieldData);
            }
        });

        if (!hasValidFields) {
            alert('Please add at least one field with label and value');
            return false;
        }

        submitButton.prop('disabled', true);

        $.ajax({
            url: action,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    alert('Note updated successfully!');
                    $('#editNoteModal').modal('hide');
                    window.location.reload();
                } else {
                    handleFormError(response, submitButton);
                }
            },
            error: function(xhr) {
                handleFormError(xhr.responseJSON, submitButton);
            }
        });

        return false;
    });
});
</script>
@endsection 
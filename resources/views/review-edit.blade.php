@extends('layouts.app')

@section('content')
    <div class="container mt-2 mb-2">
        <h2>Edit Your Review</h2>
        <form action="{{ route('reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="comment" class="form-label">Your Comment</label>
                <textarea name="comment" id="comment" class="form-control" rows="4" required>{{ old('comment', $review->comment) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Your rating *</label>

                <div id="reviewRatingDisplayWrapper" class="position-relative d-inline-block">

                    {{-- DISPLAY-ONLY STARS (Initially visible, updated on click) --}}
                    <div id="currentRatingDisplay" class="ratings-container my-1">
                        <span class="flex gap-1">
                            {{-- These stars will be updated by JS after a click --}}
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="display-star-icon" width="24" height="24"
                                    fill="{{ $i <= old('rating', $review->rating) ? '#facc15' : '#d1d5db' }}"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 .587l3.668 7.568L24 9.423l-6 5.846 1.417 8.254L12 18.897l-7.417 4.626L6 15.269 0 9.423l8.332-1.268z" />
                                </svg>
                            @endfor
                        </span>
                    </div>

                    {{-- INTERACTIVE STARS (Initially hidden, shown on hover/click) --}}
                    <div id="interactiveRatingSelect" class="select-star-rating d-none">
                        <span class="star-rating flex gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="star-rating__star-icon" width="24" height="24"
                                    fill="{{ $i <= old('rating', $review->rating) ? '#facc15' : '#d1d5db' }}"
                                    viewBox="0 0 24 24" style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg"
                                    data-value="{{ $i }}">
                                    <path
                                        d="M12 .587l3.668 7.568L24 9.423l-6 5.846 1.417 8.254L12 18.897l-7.417 4.626L6 15.269 0 9.423l8.332-1.268z" />
                                </svg>
                            @endfor
                        </span>
                    </div>



                </div>

                <input name="rating" type="hidden" id="form-input-rating" value="{{ old('rating', $review->rating) }}"
                    required />
                @error('rating')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Replace Image (optional)</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Update Review</button>
        </form>

    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reviewRatingDisplayWrapper = document.getElementById('reviewRatingDisplayWrapper');
                const currentRatingDisplay = document.getElementById('currentRatingDisplay');
                const interactiveRatingSelect = document.getElementById('interactiveRatingSelect');
                const hiddenInput = document.getElementById('form-input-rating');

                // Essential: Select the display-only stars as well
                const displayStars = currentRatingDisplay.querySelectorAll('.display-star-icon');
                const interactiveStars = interactiveRatingSelect.querySelectorAll('.star-rating__star-icon');

                // Ensure all elements exist for robustness
                if (!reviewRatingDisplayWrapper || !currentRatingDisplay || !interactiveRatingSelect || !hiddenInput ||
                    displayStars.length === 0 || interactiveStars.length === 0) {
                    console.warn(
                        "One or more star rating elements not found. Star rating interactivity might be limited.");
                    return;
                }

                // Get the initial rating from the hidden input. This value is pre-set by Blade.
                let currentRating = parseInt(hiddenInput.value) || 0;
                let tempRating = 0; // Used to store rating during hover

                const fillColor = '#facc15'; // Tailwind yellow-400
                const emptyColor = '#d1d5db'; // Tailwind gray-300

                /**
                 * Updates the visual display of a set of stars based on a given rating value.
                 * @param {NodeList} starsToUpdate The collection of SVG star elements to modify.
                 * @param {number} ratingValue The rating to display (e.g., 3 for 3 stars filled).
                 */
                function updateStarsVisual(starsToUpdate, ratingValue) {
                    starsToUpdate.forEach(star => {
                        const starValue = parseInt(star.dataset.value || star.getAttribute('data-value') ||
                        '0'); // data-value is optional for display stars
                        // For display stars, we infer value from index if data-value isn't present
                        // This ensures it works if you remove data-value from display-only stars later
                        const actualStarValue = star.dataset.value ? parseInt(star.dataset.value) : Array.from(
                            starsToUpdate).indexOf(star) + 1;

                        if (actualStarValue <= ratingValue) {
                            star.setAttribute('fill', fillColor);
                        } else {
                            star.setAttribute('fill', emptyColor);
                        }
                    });
                }

                // Initialize the interactive stars to match the current/old rating
                updateStarsVisual(interactiveStars, currentRating);
                // Also ensure display stars are correctly set based on currentRating (though Blade does this initially)
                updateStarsVisual(displayStars, currentRating);


                // --- Event Listeners for the Wrapper (to handle hover/focus) ---
                reviewRatingDisplayWrapper.addEventListener('mouseenter',
            function() { // Use mouseenter for less flickering
                    currentRatingDisplay.classList.add('d-none'); // Hide the display-only stars
                    interactiveRatingSelect.classList.remove('d-none'); // Show the interactive stars
                    updateStarsVisual(interactiveStars,
                    currentRating); // Ensure interactive stars reflect current selection
                });

                reviewRatingDisplayWrapper.addEventListener('mouseleave', function() { // Use mouseleave
                    currentRatingDisplay.classList.remove('d-none'); // Show the display-only stars
                    interactiveRatingSelect.classList.add('d-none'); // Hide the interactive stars
                    // When leaving, currentRatingDisplay already reflects the latest currentRating from the click handler
                });


                // --- Event Listeners for Interactivity on the Interactive Stars ---
                interactiveStars.forEach(star => {
                    // Mouseover: Highlight stars up to the hovered star
                    star.addEventListener('mouseover', function() {
                        tempRating = parseInt(this.dataset.value);
                        updateStarsVisual(interactiveStars, tempRating);
                    });

                    // Mouseout: Revert interactive stars to the currently selected rating (currentRating)
                    star.addEventListener('mouseout', function() {
                        updateStarsVisual(interactiveStars, currentRating);
                    });

                    // Click: Set the new rating and update the hidden input AND the display stars
                    star.addEventListener('click', function() {
                        currentRating = parseInt(this.dataset.value);
                        hiddenInput.value = currentRating; // Update the form's hidden input

                        // Update both sets of stars visually
                        updateStarsVisual(interactiveStars, currentRating); // Lock interactive stars
                        updateStarsVisual(displayStars, currentRating); // Update display-only stars

                        // Immediately switch back to display-only after click
                        currentRatingDisplay.classList.remove('d-none');
                        interactiveRatingSelect.classList.add('d-none');
                    });
                });
            });
        </script>
    @endpush
@endsection

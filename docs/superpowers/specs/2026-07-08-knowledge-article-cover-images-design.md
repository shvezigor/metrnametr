# Knowledge Article Cover Images Design

## Scope

Update the `/knowledge/...` article system so all 100 SEO/AI knowledge articles have deterministic, topic-aware cover image metadata.

This design covers knowledge articles only. It does not change `/news` database articles.

## Goals

- Give every knowledge article a relevant image filename, alt text, title, caption, and generation prompt.
- Match the Ukrainian door market with realistic commercial photography guidance.
- Avoid random stock-like images, luxury showroom scenes, abstract 3D backgrounds, text overlays, logos, and watermarks.
- Keep the current SVG image route as a fallback so pages never show broken images while raster assets are added gradually.
- Improve SEO and AI understanding through article-specific image metadata and schema image URLs.

## Approach

Add a small support helper, for example `App\Support\KnowledgeImage`, responsible for image metadata.

The helper will:

- classify each article by slug/title/cluster into a practical visual topic;
- generate a stable filename such as `yak-vybraty-vkhidni-dveri-dlia-kvartyry.webp`;
- return a preferred raster path under `/images/knowledge/`;
- check whether the raster file exists in `public/images/knowledge/`;
- fall back to `route('knowledge.image', ...)` when no raster file exists;
- return Ukrainian `alt`, `title`, and `caption`;
- return a production prompt for generating a realistic 16:9 image.

## Topic Mapping

Classification should be rule-based and easy to maintain. The first implementation will use keyword rules for these buckets:

- apartment entrance doors;
- private house entrance doors;
- interior doors;
- technical and commercial doors;
- fire-rated or security doors;
- locks, cylinders, and hardware;
- installation, measurement, openings, and mounting seams;
- production and door construction;
- insulation, thermal break, condensation, and soundproofing;
- coatings, MDF/PVC/Polymer, colors, design, and care;
- wholesale, budget, ordering, service, and general selection.

Each bucket will define:

- a short Ukrainian topic label;
- a realistic object/scene description;
- an alt text pattern;
- a caption pattern;
- a prompt body that follows the provided requirements.

## Rendering

Update `resources/views/client/knowledge/index.blade.php` and `resources/views/client/knowledge/show.blade.php` to use image metadata instead of directly calling `route('knowledge.image', ...)`.

The pages should keep current dimensions (`1200x675`) and lazy/eager loading behavior. The visible caption should describe the cover as a thematic image rather than a technical SVG illustration.

## SEO And Schema

Update knowledge article schema image resolution so `SeoContent::articleImageUrl()` prefers the raster image URL when the file exists and otherwise returns the existing SVG route.

The article schema should therefore point at the same image users see.

## Asset Workflow

This iteration prepares the website to consume raster files, but does not require all 100 images to be generated in one step.

Final image assets should be saved as compressed WebP or JPG files in:

`public/images/knowledge/`

Recommended image format:

- landscape 16:9;
- 1200x675 or larger;
- realistic commercial photo;
- no in-image text;
- no logos;
- no watermarks.

## Validation

Add focused tests for:

- every `SeoContent::articles()` item has image metadata;
- every image metadata record has filename, src, alt, title, caption, and prompt;
- fallback image URLs still resolve to the SVG route when no raster file exists;
- generated prompts contain the main constraints: realistic photo, Ukrainian market context, horizontal 16:9, no text, no logos, no watermarks.

Run the existing PHPUnit suite after implementation.

## Out Of Scope

- Editing `/news` database articles.
- Writing generated image binaries directly into this design commit.
- Adding new UI frameworks or image generation dependencies.
- Replacing the existing `/knowledge/{slug}/image.svg` fallback route.

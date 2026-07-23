const test = require('node:test');
const assert = require('node:assert/strict');
const {
  matchesWorkFilter,
  nextWorkImageIndex,
} = require('../../resources/client/js/real-works');

test('all filter keeps every case visible', () => {
  assert.equal(matchesWorkFilter(['interior', 'lutsk'], 'all'), true);
});

test('category filter only matches declared case tags', () => {
  assert.equal(matchesWorkFilter(['interior', 'lutsk'], 'interior'), true);
  assert.equal(matchesWorkFilter(['interior', 'lutsk'], 'entrance'), false);
});

test('lightbox index wraps in both directions', () => {
  assert.equal(nextWorkImageIndex(1, 2, 1), 0);
  assert.equal(nextWorkImageIndex(0, 2, -1), 1);
});

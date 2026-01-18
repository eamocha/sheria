import React, { lazy, Suspense } from 'react';

const LazyNotes = lazy(() => import('./Notes'));

const Notes = props => (
  <Suspense fallback={null}>
    <LazyNotes {...props} />
  </Suspense>
);

export default Notes;

import React, { lazy, Suspense } from 'react';

const LazyAPNoteRow = lazy(() => import('./APNoteRow'));

const APNoteRow = props => (
  <Suspense fallback={null}>
    <LazyAPNoteRow {...props} />
  </Suspense>
);

export default APNoteRow;

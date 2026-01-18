import React, { lazy, Suspense } from 'react';

const LazyAPLegalCaseNoteAddForm = lazy(() => import('./APLegalCaseNoteAddForm'));

const APLegalCaseNoteAddForm = props => (
  <Suspense fallback={null}>
    <LazyAPLegalCaseNoteAddForm {...props} />
  </Suspense>
);

export default APLegalCaseNoteAddForm;

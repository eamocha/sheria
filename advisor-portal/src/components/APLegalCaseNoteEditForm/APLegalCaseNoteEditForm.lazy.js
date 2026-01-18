import React, { lazy, Suspense } from 'react';

const LazyAPLegalCaseNoteEditForm = lazy(() => import('./APLegalCaseNoteEditForm'));

const APLegalCaseNoteEditForm = props => (
  <Suspense fallback={null}>
    <LazyAPLegalCaseNoteEditForm {...props} />
  </Suspense>
);

export default APLegalCaseNoteEditForm;

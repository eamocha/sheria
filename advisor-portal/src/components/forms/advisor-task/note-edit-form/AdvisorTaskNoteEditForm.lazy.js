import React, { lazy, Suspense } from 'react';

const LazyAdvisorTaskNoteEditForm = lazy(() => import('./AdvisorTaskNoteEditForm'));

const AdvisorTaskNoteEditForm = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTaskNoteEditForm {...props} />
  </Suspense>
);

export default AdvisorTaskNoteEditForm;

import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageExternalReferenceAddForm = lazy(() => import('./LitigationCaseStageExternalReferenceAddForm'));

const LitigationCaseStageExternalReferenceAddForm = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageExternalReferenceAddForm {...props} />
  </Suspense>
);

export default LitigationCaseStageExternalReferenceAddForm;

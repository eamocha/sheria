import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageExternalReferences = lazy(() => import('./LitigationCaseStageExternalReferences'));

const LitigationCaseStageExternalReferences = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageExternalReferences {...props} />
  </Suspense>
);

export default LitigationCaseStageExternalReferences;

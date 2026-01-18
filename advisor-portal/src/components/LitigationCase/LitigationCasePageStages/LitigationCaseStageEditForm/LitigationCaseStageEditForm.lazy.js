import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageEditForm = lazy(() => import('./LitigationCaseStageEditForm'));

const LitigationCaseStageEditForm = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageEditForm {...props} />
  </Suspense>
);

export default LitigationCaseStageEditForm;

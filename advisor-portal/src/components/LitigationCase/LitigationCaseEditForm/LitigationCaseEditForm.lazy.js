import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseEditForm = lazy(() => import('./LitigationCaseEditForm'));

const LitigationCaseEditForm = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseEditForm {...props} />
  </Suspense>
);

export default LitigationCaseEditForm;

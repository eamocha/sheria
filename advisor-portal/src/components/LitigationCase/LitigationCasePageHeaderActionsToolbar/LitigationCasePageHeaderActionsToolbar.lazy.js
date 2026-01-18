import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageHeaderActionsToolbar = lazy(() => import('./LitigationCasePageHeaderActionsToolbar'));

const LitigationCasePageHeaderActionsToolbar = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageHeaderActionsToolbar {...props} />
  </Suspense>
);

export default LitigationCasePageHeaderActionsToolbar;

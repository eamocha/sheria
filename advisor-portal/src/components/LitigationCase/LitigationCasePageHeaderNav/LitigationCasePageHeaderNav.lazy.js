import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageHeaderNav = lazy(() => import('./LitigationCasePageHeaderNav'));

const LitigationCasePageHeaderNav = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageHeaderNav {...props} />
  </Suspense>
);

export default LitigationCasePageHeaderNav;

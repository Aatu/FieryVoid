import * as React from "react";

class Rotate extends React.Component {
  render() {
    const { color = "#fff" } = this.props;

    return (
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 16 16"
        width="100%"
        height="100%"
      >
        <path
          d="M8,0C3.582,0,0,3.582,0,8s3.582,8,8,8s8-3.582,8-8S12.418,0,8,0z M8,14 c-2.169,0-4.07-1.15-5.124-2.876L2,12V8h2h1h1L4.354,9.646C4.981,11.034,6.378,12,8,12c1.863,0,3.43-1.273,3.874-3h2.043 C13.441,11.838,10.973,14,8,14z M12,8h-1h-1l1.646-1.646C11.02,4.966,9.622,4,8,4C6.136,4,4.57,5.274,4.126,7H2.083 C2.559,4.162,5.027,2,8,2c2.169,0,4.07,1.151,5.124,2.876L14,4v4H12z"
          fill={color}
        />
      </svg>
    );
  }
}

export default Rotate;

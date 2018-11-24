import * as React from "react";

class Cancel extends React.Component {
  render() {
    const { color = "#fff", size = "90%" } = this.props;

    return (
      <svg
        xmlns="http://www.w3.org/2000/svg"
        id="cancel"
        viewBox="0 0 512 512"
        width={size}
        height={size}
      >
        <path
          id="svg-path"
          d="M256,0C114.833,0,0,114.844,0,256s114.833,256,256,256s256-114.844,256-256S397.167,0,256,0z M256,426.667 c-94.104,0-170.667-76.563-170.667-170.667c0-31.596,8.785-61.112,23.814-86.52L342.52,402.853 C317.112,417.882,287.596,426.667,256,426.667z M402.853,342.52L169.48,109.147c25.408-15.029,54.923-23.814,86.52-23.814 c94.104,0,170.667,76.563,170.667,170.667C426.667,287.596,417.882,317.112,402.853,342.52z"
          fill={color}
        />
      </svg>
    );
  }
}

export default Cancel;
